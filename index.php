<?php

require_once __DIR__ . '/vendor/autoload.php';

// .env tiene prioridad; si no existe, se usa `env` (mismo formato; útil en clones sin .env).
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, ['.env', 'env']);
// No pisar variables ya definidas (p. ej. docker-compose environment: DB_HOST=db)
$dotenv->safeLoad();

require_once __DIR__ . '/core/bootstrap_security.php';
gp_configure_session_ini();
session_start();

require_once __DIR__ . '/core/helpers/url.php';
gp_send_security_headers();

require_once __DIR__ . '/app/modelos/database.php';
require_once __DIR__ . '/app/modelos/admin_config.php';
$envSessionTtl = (int) ($_ENV['SESSION_IDLE_TIMEOUT_SECONDS'] ?? getenv('SESSION_IDLE_TIMEOUT_SECONDS') ?: 0);
$sessionTtl = $envSessionTtl > 0 ? $envSessionTtl : AdminConfig::getInt('session_idle_timeout_seconds', 2700);
$sessionTtl = min(max($sessionTtl, 60), 86400 * 7);
if (!empty($_SESSION['usuario_id']) && $sessionTtl > 0) {
    $last = (int) ($_SESSION['last_activity_at'] ?? time());
    if ((time() - $last) > $sessionTtl) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
        }
        session_destroy();

        if (str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json')) {
            http_response_code(440);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'error' => 'Sesión cerrada por inactividad.']);
            exit;
        }

        header('Location: ' . url('/login') . '?error=' . rawurlencode('Sesión cerrada por inactividad. Vuelve a iniciar sesión.'));
        exit;
    }
    $_SESSION['last_activity_at'] = time();
}

require_once __DIR__ . '/app/modelos/usuario.php';

if (!empty($_SESSION['usuario_id'])) {
    $bloqueoSesion = Usuario::estadoBloqueo((int) $_SESSION['usuario_id']);
    if (!empty($bloqueoSesion['bloqueado'])) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
        }
        session_destroy();
        if (
            str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json')
            || str_contains((string) ($_SERVER['REQUEST_URI'] ?? ''), '/sesion/estado')
        ) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'ok' => true,
                'authenticated' => false,
                'forced_logout' => true,
                'logout_kind' => $bloqueoSesion['tipo'] === 'P' ? 'baja_permanente' : 'baja_cuenta',
                'message' => $bloqueoSesion['tipo'] === 'P'
                    ? 'Tu cuenta ha sido dada de baja permanentemente. Se ha cerrado la sesión.'
                    : 'Tu cuenta ha sido dada de baja. Se ha cerrado la sesión.',
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        header('Location: ' . url('/login') . '?error=' . rawurlencode('Tu cuenta está dada de baja. Contacta con recepción.'));
        exit;
    }
}


use Bramus\Router\Router;

$router = new Router();
$router->setBasePath(router_server_base_path());

require_once "routers/web.php";

$router->set404(function () {
    require_once __DIR__ . '/app/controladores/ErrorControlador.php';
    (new ErrorControlador())->notFound();
});

$router->run();