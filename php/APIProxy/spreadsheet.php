<?php
namespace DriveForm\APIProxy\Google;

class SpreadSheet {
    private $CLIENT_ID;
    private $CLIENT_SECRET;
    private $redirect_uri;
    private $client;
    private $service;

    function __construct($opt = NULL) {
        if (!is_null($opt) &&
            is_array($opt) &&
            array_key_exists('CLIENT_ID', $opt) &&
            array_key_exists('CLIENT_SECRET', $opt) &&
            array_key_exists('REDIRECT_URI', $opt)) {
            echo "Override";
        } else {
            $this->CLIENT_ID = \DriveForm\Config\CLIENT_ID;
            $this->CLIENT_SECRET = \DriveForm\Config\CLIENT_SECRET;
            $this->redirect_uri = \DriveForm\Config\REDIRECT_URI;
        }

        $this->client = new \Google_Client();
        $this->client->setClientId($this->CLIENT_ID);
        $this->client->setClientSecret($this->CLIENT_SECRET);
        $this->client->setRedirectUri($this->redirect_uri);
        $this->client->setScopes(array(
            'https://www.googleapis.com/auth/drive',
            'email',
            'profile'));
        $this->service = new \Google_Service_Drive($this->client);
    }
    public function init() {
        echo "Contact the SpreadSheet Provider.";
    }
}
