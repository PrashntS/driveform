<?php
namespace DriveForm\APIProxy\Google;

class SpreadSheet {
    private $CLIENT_ID;
    private $CLIENT_SECRET;

    function __construct($opt = NULL) {
        if (!is_null($opt) &&
            is_array($opt) &&
            array_key_exists('CLIENT_ID', $opt) &&
            array_key_exists('CLIENT_SECRET', $opt)) {
            echo "Override";
        } else {
            $this->CLIENT_ID = \DriveForm\Config\CLIENT_ID;
            $this->CLIENT_SECRET = \DriveForm\Config\CLIENT_SECRET;
        }
    }
    public function init() {
        echo "Contact the SpreadSheet Provider.";
    }
}
