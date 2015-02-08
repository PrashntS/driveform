<?php
namespace DriveForm\Delegate;

/**
 * Builds an access to the Service Authentication retrieved for Google API.
 * Provides access to Client ID, Client Email, Private Key and Private Key ID.
 * @author Prashant Sinha <prashant@ducic.ac.in>
 */
class Service_Auth {

    /**
     * Contains the File Location of the auth.json file. It is assigned dynamically,
     * however it doesn't check for the existence of the file, it simply assumes.
     * @var String
     */
    private static $AUTH_FILE;

    /**
     * Container for the JSON decoded array of Auth Data.
     * @var Mixed
     */
    private static $auth;

    /**
     * Constructs the Object, assigns the Auth File location, and Retrieves it.
     */
    function __construct() {
        self::$AUTH_FILE = dirname(__FILE__).'/auth.json';

        if (file_exists(self::$AUTH_FILE)) {
            $auth_file = file_get_contents(self::$AUTH_FILE);
            self::$auth = json_decode($auth_file, true);
        } else {
            throw new \DriveForm\Exception\Missing_AUTH_File_Exception("Check auth.json.");
        }

        if (!is_array(self::$auth)) {
            throw new \DriveForm\Exception\Invalid_AUTH_File_Exception("Check auth.json.");
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
