<?php
namespace DriveForm\Delegate;

/**
 * Provides access to User configurations.
 * @author Prashant Sinha <prashant@ducic.ac.in>
 */
class Config {

    /**
     * Container for the JSON decoded array of Config Data.
     * @var Mixed
     */
    private static $config;

    /**
     * Constructs the Object, and gets the Config Fields.
     */
    function __construct() {
        $config_location = dirname(__FILE__).'/config.json';

        if (file_exists($config_location)) {
            $file_content = file_get_contents($config_location);

            $config_arr = json_decode($file_content, true);

            if (is_array($config_arr)) {
                self::$config = $config_arr;
            } else throw new \DriveForm\Exception\Invalid_Configuration("config.json is not a valid Configuration File.");
        } else throw new \DriveForm\Exception\Missing_Configuration_File("config.json must be in the App Root.");
    }

    /**
     * Getter for the Config Fields.
     * @param  String $key The field header.
     * @return Mixed       The field content.
     */
    function __get($key) {
        return array_key_exists($key, self::$config) ? self::$config[$key] : NULL;
    }
}

/**
 * Builds an access to the Service Authentication retrieved for Google API.
 * Provides access to Client ID, Client Email, Private Key and Private Key ID.
 * @author Prashant Sinha <prashant@ducic.ac.in>
 */
class Service_Auth {

    /**
     * Container for the JSON decoded array of Auth Data.
     * @var Mixed
     */
    private static $auth;

    /**
     * Container for the Public Key Certificate.
     * @var Binary
     */
    private static $p12;

    /**
     * Constructs the Object, assigns the Auth File location, and Retrieves it.
     */
    function __construct() {
        global $_CONFIG;

        $auth_file = realpath(dirname(__FILE__).'/'.$_CONFIG->auth_json_file);
        $auth_p12_file = realpath(dirname(__FILE__).'/'.$_CONFIG->auth_certificate_p12);

        if (file_exists($auth_file)) {
            $auth_file = file_get_contents($auth_file);
            self::$auth = json_decode($auth_file, true);
        } else {
            throw new \DriveForm\Exception\Missing_AUTH_File_Exception("Check auth.json.");
        }

        if (!is_array(self::$auth)) {
            throw new \DriveForm\Exception\Invalid_AUTH_File_Exception("Check auth.json.");
        }

        if (file_exists($auth_p12_file)) {
            self::$p12 = file_get_contents($auth_p12_file);
        } else {
            throw new \DriveForm\Exception\Missing_P12_Certificate("Obtain the Certificate from your Google Dev. Console.");
        }
    }

    /**
     * Getter for the Auth data fields.
     * @param  String $key The Key of the Data Field.
     * @return Mixed       The Value associated to the Key. If key doesn't exists
     *                     yields NULL.
     */
    public function __get($key) {
        return array_key_exists($key, self::$auth) ? self::$auth[$key] : NULL;
    }

    /**
     * Getter for the Public Key file content.
     */
    public function P12() {
        return self::$p12;
    }
}

/**
 * Interface to store the Server Side Flat File Persistence. Saves various session
 * data into a file on server, eliminating the need for any DataBase.
 * @author Prashant Sinha <prashant@ducic.ac.in>
 */
class State {

    /**
     * Contains the State Array for the current runtime.
     * @var Array
     */
    private static $state;

    /**
     * Location to the State File. Assigned dynamically, but doesn't check the
     * existence of the file.
     * @var String
     */
    private static $STATE_FILE;

    /**
     * Flag that determines if there was any change in State data that needs to
     * be written in the file. Saves unnecessary writes, as well as protects the
     * missed information issue, inherent from Concurrency.
     * @var Boolean
     */
    private static $changed;

    /**
     * Constructs the State object, and initializes the file and state field.
     */
    function __construct() {
        self::$STATE_FILE = dirname(__FILE__).'/state.json';
        self::$changed = false;

        if (file_exists(self::$STATE_FILE)) {
            $state_file = file_get_contents(self::$STATE_FILE);
            self::$state = json_decode($state_file, true);
        }

        if (!file_exists(self::$STATE_FILE) || !is_array(self::$state)) {
            self::$changed = true;
            self::$state = ["Warning" => "Auto-generated. DO NOT EDIT."];
        }
    }

    /**
     * If the $changed flag is set true, writes the changes to disk once State
     * object is deleted.
     */
    function __destruct() {
        if (self::$changed) {
            $state_json = json_encode(self::$state, JSON_PRETTY_PRINT);
            file_put_contents(self::$STATE_FILE, $state_json, LOCK_EX);
        }
    }

    /**
     * Returns the State Field.
     * @param  String $key The Key Identifier of the Field.
     * @return Mixed       The value associated with the Key.
     */
    public function __get($key) {
        return array_key_exists($key, self::$state) ? self::$state[$key] : NULL;
    }

    /**
     * Sets the State Field, and updates the $changed flag to mark a write routine.
     * @param String $key   The Key Identifier of the Field.
     * @param Mixed  $value The Value that has to be associated with the Key.
     */
    public function __set($key, $value) {
        self::$changed = true;
        self::$state[$key] = $value;
    }

    /**
     * Checks whether the Field exists in State Data.
     * @param  String  $key The Key Identifier of the Field.
     * @return boolean      Existence of Key.
     */
    public function __isset($key) {
        return isset(self::$state[$key]);
    }

    /**
     * Deletes the Field.
     * @param String $key The Key Identifier of the Field.
     */
    public function __unset($key) {
        if (array_key_exists($key, self::$state)) {
            unset(self::$state[$key]);
        }
    }
}
