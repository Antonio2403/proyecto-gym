<?php

session_start();

require_once "vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once "app/modelos/database.php";


use Bramus\Router\Router;

$router = new Router();

require_once "routers/web.php";

$router->run();