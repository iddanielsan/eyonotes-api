<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(dirname(__FILE__)).DS);

require('config.php');
require BASE_PATH.'vendor/autoload.php';

$app = System\App::instance();
$app->request = System\Request::instance();
$app->route	= System\Route::instance($app->request);
$route = $app->route;

$dbCapsule = new App\Models\dbCapsule;
require('routes.php');

$route->end();