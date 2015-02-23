<?php
namespace DriveForm;

require 'lib/delegate.php';
require 'lib/exceptions.php';
require 'vendor/google-api-php-client/autoload.php';
require 'vendor/slim_framework/Slim/Slim.php';
require 'vendor/PHPMailer/PHPMailerAutoload.php';
require 'lib/database.php';
require 'lib/util.php';
require 'lib/actions.php';
require 'view/form.php';

\Slim\Slim::registerAutoloader();

$_CONFIG = new \DriveForm\Delegate\Config();
$_STATE  = new \DriveForm\Delegate\State();
$_APP    = new \Slim\Slim();

$_APP->get('/', function () {
    View\Form\init();
});

$_APP->get('/setup', function () {
    Util\Setup::Database();
});

$_APP->post('/register', function () use($_APP) {
    $q = Action\Model::register();
    if ($q[error]) {
        $_APP->response->setStatus(400);
        View\Form\error($q['error_field']);
    } else {
        $_APP->response->setStatus(202);
        Action\Email::acknowledge($q);
        View\Form\success($q['reg_id']);
    }
});

$_APP->post('/api/register', function() use($_APP) {
    $q = Action\Model::register();
    if ($q[error]) {
        $_APP->response->setStatus(400);
    } else {
        Action\Email::acknowledge();
        $_APP->response->setStatus(202);
    }
    $_APP->response->headers->set('Content-Type', 'application/json');
    $_APP->response->setBody(json_encode($q));
});

$_APP->get('/api/count/:id', function($id) use($_APP) {
    $q = Action\Model::count_registrations($id);
        $Handle = new \DriveForm\Database\Client();
    $_APP->response->setStatus(200);
    $_APP->response->headers->set('Content-Type', 'application/json');
    $_APP->response->headers->set('Access-Control-Allow-Origin', '*');
    $_APP->response->setBody(json_encode([
        "booked" => $q[0],
        "remains" => $q[1],
        "registrations_accepted" => $q[2]
    ]));
});

$_APP->get('/uploaded_dd/:name', function($name) use($_APP) {
    $pic = dirname(__FILE__).'/user/'.$name;

    if (file_exists($pic) && is_readable($pic)) {
        $ext = explode(".", $name);
        switch ($ext[1]) {
            case 'jpg':
            case 'jpeg':
                $mime = 'image/jpeg';
                break;
            case 'gif':
                $mime = 'image/gif';
                break;
            case 'png':
                $mime = 'image/png';
                break;
            case 'tiff':
            case 'tif':
                $mime = 'image/tiff';
                break;
            default:
                $mime = false;
        }
        $_APP->response->headers->set('Content-type', $mime);
        $_APP->response->headers->set('Content-length', filesize($pic));
        $file = @ fopen($pic, 'rb');
        if ($file) {
            echo file_get_contents($file);
        }

    } else {
        //$_APP->response->setStatus(404);
        echo "File does not exists.";
    }
});

$_APP->run();
