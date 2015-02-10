<?php
namespace DriveForm;

require 'lib/delegate.php';
require 'lib/exceptions.php';
require 'vendor/google-api-php-client/autoload.php';
require 'vendor/slim_framework/Slim/Slim.php';
require 'lib/database.php';
require 'lib/util.php';
require 'lib/actions.php';

\Slim\Slim::registerAutoloader();

$_CONFIG = new \DriveForm\Delegate\Config();
$_AUTH   = new \DriveForm\Delegate\Service_Auth();
$_STATE  = new \DriveForm\Delegate\State();
$_APP    = new \Slim\Slim();

/*
$_APP->get('/', function () {
    echo "Index Route";
    $a = new \DriveForm\APIProxy\Google\Spreadsheet();
});
*/

$_APP->get('/', function () {
    # Serving the static files
    
    //
    //APIProxy\Google\Directory::create();
});

$_APP->get('/setup', function () {
    Util\Setup::Database();
});

$_APP->post('/api/upload', function() {
    # Upload the DD here, to NOT to Google Drive. Upload it in the folder.
    //var_dump($_FILES);
    var_dump(Action\Model::register());
    //var_dump(file_get_contents($_FILES['fileUpload']['tmp_name']));
});



$_APP->run();
