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
    var_dump(Action\Model::validate_registration("s"));
    //
    //APIProxy\Google\Directory::create();
});

$_APP->get('/setup', function () {
    Util\Setup::Database();
});

$_APP->post('/api/register', function() use($_APP) {
    $q = Action\Model::register();
    if ($q[error]) {
        $_APP->response->setStatus(400);
    } else {
        $_APP->response->setStatus(202);
    }
    $_APP->response->setBody(json_encode($q));
});

$_APP->get('/api/count/:id', function($id) use($_APP) {
    $q = Action\Model::count_registrations($id);
        $Handle = new \DriveForm\Database\Client();
    echo "<pre>";
    echo json_encode($Handle->query("SELECT * FROM workshop_registrations")->fetchAll(\PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
    $_APP->response->setStatus(200);
    $_APP->response->setBody(json_encode([
        "booked" => $q[0],
        "remains" => $q[1],
        "registrations_accepted" => $q[2]
    ]));
});

$_APP->run();
