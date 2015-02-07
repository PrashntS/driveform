<?php
namespace DriveForm;

require 'drive.php';
require 'vendor/google-api-php-client/autoload.php';

# Routing Cases
$URI = preg_split('/[\/\?]/', preg_replace("/[\/]+/", "/", $_SERVER['REQUEST_URI']));
const BASE = 2;

switch(strtolower(isset($URI[BASE]) ? $URI[BASE] : False)) {
    case 'form':
        Google\API\SpreadSheet\init();
        break;
    default:
        echo "Serve Index File here.";
}