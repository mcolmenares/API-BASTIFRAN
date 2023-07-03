<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');


require '../vendor/autoload.php';
require '../src/config/db.php';

$app = new \Slim\App;

// Ruta Admin
require '../src/rutas/Admin.php';

$app->run();