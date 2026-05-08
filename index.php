<?php

session_start();

require_once "vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
// No pisar variables ya definidas (p. ej. docker-compose environment: DB_HOST=db)
$dotenv->safeLoad();
require_once __DIR__ . '/core/helpers/url.php';

require_once "app/modelos/database.php";


use Bramus\Router\Router;

$router = new Router();
$router->setBasePath(router_server_base_path());

require_once "routers/web.php";

$router->set404(function () {
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><title>No encontrado</title></head><body>';
    echo '<p>Página no encontrada. <a href="' . htmlspecialchars(url('/')) . '">Volver al inicio</a></p>';
    echo '</body></html>';
});

$router->run();