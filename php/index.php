<?php
namespace DriveForm;

require 'delegate.php';
require 'exceptions.php';
require 'vendor/google-api-php-client/autoload.php';
require 'vendor/slim_framework/Slim/Slim.php';
require 'APIProxy/spreadsheet.php';

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
    # Serving the static files.
    
})

$_APP->run();
