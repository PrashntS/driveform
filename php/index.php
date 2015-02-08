<?php
namespace DriveForm;

require 'delegate.php';
require 'exceptions.php';
require 'vendor/google-api-php-client/autoload.php';
require 'APIProxy/spreadsheet.php';

$auth = new \DriveForm\Delegate\Service_Auth();
$state = new \DriveForm\Delegate\State();

# Routing Cases
$URI = preg_split('/[\/\?]/', preg_replace("/[\/]+/", "/", $_SERVER['REQUEST_URI']));
const BASE = 2;

switch(strtolower(isset($URI[BASE]) ? $URI[BASE] : False)) {
    case 'form':
        //$spreadsheet = new APIProxy\Google\SpreadSheet();
        //echo file_get_contents("DriveForm Access-30e4e8b93b86.p12");
        APIProxy\Google\initClient();

        break;
    default:
        echo "Serve Index File here.";
}
