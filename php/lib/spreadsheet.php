<?php
namespace DriveForm\APIProxy\Google;

class SpreadSheet {
    private $client;
    private $service;

    function __construct() {
        $this->client = new Client();

        $this->service = new \Google_Service_Drive($this->client->scope);
        //$this->create("Test");
        $files = $this->service->files->listFiles();

        echo "<pre>";
        var_dump(json_encode($files->getItems(), JSON_PRETTY_PRINT));

        //$result = array_merge($result, $files->getItems());
        //$this->get_file_by_id("0B77hn52Njlz9am9JSkxIdEVtdG8");
    }

    #public function insert($name, $parent, )

    public function create($name, $parent = NULL) {
        $file = new \Google_Service_Drive_DriveFile();
        $file->setTitle($name);
        $file->setDescription("DRIVE");
        $file->setMimeType("text/csv");

        try {
            $data = file_get_contents(dirname(dirname(__FILE__)) . "/form_template.csv");

            $createdFile = $this->service->files->insert(
                $file,
                array(
                'data' => "LOL,LOLL",
                'mimeType' => "text/csv",
                'convert' => true));

            echo $createdFile->getId();

            return $createdFile;
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
    }

    public function get_file_by_id($id) {
        $file = $this->service->files->get($id);

        echo $file->getTitle();
    }

}

class Directory {
    private $directory;
    private static $client;
    private static $service;

    public static function setup() {

    }

    public static function create() {
        global $_CONFIG, $_STATE;

        self::$client = new Client();

        self::$service = new \Google_Service_Drive(self::$client->scope);

        $file = new \Google_Service_Drive_DriveFile();
        $file->setTitle($_CONFIG->dir_name);
        $file->setMimeType("application/vnd.google-apps.folder");

        $parent = new \Google_Service_Drive_ParentReference();
        $parent->setId("0BwvbUtDRRjGJd2c2cWZKc09YUUU");
        $file->setParents(array($parent));

        try {
            $createdFile = self::$service->files->insert(
                $file,
                ['mimeType' => "application/vnd.google-apps.folder"]);

            echo $createdFile->getId();

            $_STATE->dir_id = $createdFile->getId();

            return $createdFile;
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
    }
}

class Util {
    public static function upload() {
        //
    }
}

class Client {
    public $scope;

    function __construct() {
        global $_CONFIG, $_AUTH, $_STATE;

        $this->scope = new \Google_Client();
        $this->scope->setApplicationName($_CONFIG->application_name);

        if (isset($_STATE->service_token)) {
            $this->scope->setAccessToken($_STATE->service_token);
        }

        $credentials = new \Google_Auth_AssertionCredentials(
            $_AUTH->client_email,
            $_CONFIG->scope,
            $_AUTH->P12()
        );

        $this->scope->setAssertionCredentials($credentials);

        if ($this->scope->getAuth()->isAccessTokenExpired()) {
            # Renew the Access token.
            $this->scope->getAuth()->refreshTokenWithAssertion($credentials);
        }

        $_STATE->service_token = $this->scope->getAccessToken();
    }
}
