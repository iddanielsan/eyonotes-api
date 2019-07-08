<?php

header('Content-Type: application/json');
header('Content-Type: text/html; charset=utf-8');

define('DS', DIRECTORY_SEPARATOR, true);
define('BASE_PATH', dirname(dirname(__FILE__)).DS, TRUE);

require('config.php');
require BASE_PATH.'vendor/autoload.php';

$app = System\App::instance();
$app->request = System\Request::instance();
$app->route	= System\Route::instance($app->request);
$route = $app->route;

$dbCapsule = new App\Models\dbCapsule;
require('routes.php');

$route->end();