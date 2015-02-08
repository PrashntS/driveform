<?php
namespace DriveForm\Delegate;

class Auth {
    private static $AUTH_FILE;
    private static $auth;

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

    public function __get($key) {
        return array_key_exists($key, self::$auth) ? self::$auth[$key] : NULL;
    }
}

class State {
    private static $state;
    private static $STATE_FILE;
    private static $changed;

    function __construct() {
        self::$STATE_FILE = dirname(__FILE__).'/state.json';
        self::$changed = false;

        if (file_exists(self::$STATE_FILE)) {
            $state_file = file_get_contents(self::$STATE_FILE);
            self::$state = json_decode($state_file, true);
        } else {
            self::$state = [];
        }

        if (!is_array(self::$state)) {
            self::$state = [];
        }
    }

    function __destruct() {
        if (self::$changed) {
            $state_json = json_encode(self::$state);
            file_put_contents(self::$STATE_FILE, $state_json, LOCK_EX);
        }
    }

    public function __get($key) {
        return array_key_exists($key, self::$state) ? self::$state[$key] : NULL;
    }

    public function __set($key, $value) {
        self::$changed = true;
        self::$state[$key] = $value;
    }

    public function __isset($key) {
        return isset(self::$state[$key]);
    }

    public function __unset($key) {
        if (array_key_exists($key, self::$state)) {
            unset(self::$state[$key]);
        }
    }
}
