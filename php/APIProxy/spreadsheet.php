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
        $this->create("Test");
    }
    public function create($name, $parent = NULL) {
        $file = new \Google_Service_Drive_DriveFile();
        $file->setTitle($name);
        //$file->setDescription($description);
        //$file->setMimeType($mimeType);

        try {
            $data = file_get_contents(dirname(dirname(__FILE__)) . "/form_template.csv");

            $createdFile = $this->service->files->insert($file, array(
                'data' => $data,
                'mimeType' => "text/csv",
                'convert' => true));

            // Uncomment the following line to print the File ID
            print 'File ID: %s' % $createdFile->getId();

            return $createdFile;
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
    }
    public function init() {
        echo "Contact the SpreadSheet Provider.";
    }
}

function initClient() {
    $client = new Google_Client();
    $client->setApplicationName("DriveForm");
    
}