<?php

require_once "core/Controller.php";
require_once "app/modelos/usuario.php";
require_once "app/modelos/admin_config.php";


class LoginControlador extends Controller
{
    public function mostrarLogin()
    {
        $this->renderFrontend("frontend/login");
    }
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/login'));
            exit;
        }

        $ident = trim((string) ($_POST['identificador'] ?? $_POST['email'] ?? ''));
        $clave = (string) ($_POST['clave'] ?? '');

        if ($ident === '' || $clave === '') {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Indica tu email o DNI y la contraseña'));
            exit;
        }

        if (!fv_login_identificador($ident)) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Introduce un email válido o un DNI/NIE correcto'));
            exit;
        }

        $usuario = Usuario::obtenerPorLoginIdentificador($ident);

        if (!$usuario || !password_verify($clave, $usuario->getClave())) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Credenciales incorrectas'));
            exit;
        }

        $bloqueo = Usuario::estadoBloqueo((int) $usuario->getId());
        if (!empty($bloqueo['bloqueado'])) {
            $msg = $bloqueo['tipo'] === 'T'
                ? 'Tu cuenta está dada de baja. Crea un ticket de gestión y acude a recepción para que te faciliten el código de reactivación.'
                : 'Tu cuenta está dada de baja permanentemente. Contacta con recepción.';
            header('Location: ' . url('/login') . '?error=' . rawurlencode($msg));
            exit;
        }

        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare('SELECT * FROM administradores WHERE usuario_id = :id');
        $stmt->bindValue(':id', $usuario->getId());
        $stmt->execute();
        $esAdmin = (bool) $stmt->fetch();

        $stmt = $conexion->prepare('SELECT * FROM monitores WHERE usuario_id = :id');
        $stmt->bindValue(':id', $usuario->getId());
        $stmt->execute();
        $esMonitor = (bool) $stmt->fetch();

        $stmt = $conexion->prepare('SELECT id FROM fisioterapeutas WHERE usuario_id = :id LIMIT 1');
        $stmt->bindValue(':id', $usuario->getId(), PDO::PARAM_INT);
        $stmt->execute();
        $esFisio = (bool) $stmt->fetch();

        $stmt = $conexion->prepare('SELECT id FROM clientes WHERE usuario_id = :id LIMIT 1');
        $stmt->bindValue(':id', $usuario->getId(), PDO::PARAM_INT);
        $stmt->execute();
        $esCliente = (bool) $stmt->fetch();

        if (!$esAdmin && !$esMonitor && !$esFisio && $esCliente && $usuario->getEmailConfirmado() === 0) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Tu cuenta aún no está activa. Revisa tu correo y pulsa el enlace de confirmación antes de iniciar sesión.'));
            exit;
        }

        session_regenerate_id(true);
        Usuario::limpiarTokensRecuperacion((int) $usuario->getId());

        $mustChange = false;
        if (!fv_clave_fuerte($clave)) {
            $mustChange = true;
        } else {
            $meta = Usuario::obtenerMetaClave((int) $usuario->getId());
            $changedAt = $meta['password_changed_at'] ?? null;
            if ($changedAt === null || (string) $changedAt === '') {
                Usuario::marcarPasswordCambiadaAhora((int) $usuario->getId());
            } else {
                $rotDays = AdminConfig::getInt('password_rotation_days', 90);
                if ($rotDays > 0) {
                    $ts = strtotime((string) $changedAt);
                    if ($ts !== false && (time() - $ts) >= $rotDays * 86400) {
                        $mustChange = true;
                    }
                }
            }
        }

        $_SESSION['usuario_id'] = $usuario->getId();
        $_SESSION['email'] = $usuario->getEmail();
        $_SESSION['nombre'] = $usuario->getNombre();

        $_SESSION['rol'] = 'cliente';

        if ($mustChange) {
            $_SESSION['password_must_change'] = 1;
        } else {
            unset($_SESSION['password_must_change']);
        }

        if ($esAdmin) {
            $_SESSION['rol'] = 'admin';
            header('Location: ' . url('/inicioAdmin'));
            exit;
        }

        if ($esMonitor) {
            $_SESSION['rol'] = 'monitor';
            header('Location: ' . url('/inicioMonitor'));
            exit;
        }

        if ($esFisio) {
            $_SESSION['rol'] = 'fisio';
            header('Location: ' . url('/fisio'));
            exit;
        }

        header('Location: ' . url('/inicioUsuario'));
        exit;
    }
    public function logout()
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }
        session_destroy();
        header('Location: ' . url('/login'));
        exit;
    }

    public function estadoSesion(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $uid = (int) ($_SESSION['usuario_id'] ?? 0);
        if ($uid <= 0) {
            echo json_encode(['ok' => true, 'authenticated' => false], JSON_UNESCAPED_UNICODE);
            return;
        }

        $bloqueo = Usuario::estadoBloqueo($uid);
        if (!empty($bloqueo['bloqueado'])) {
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    (bool) $params['secure'],
                    (bool) $params['httponly']
                );
            }
            session_destroy();
            echo json_encode([
                'ok' => true,
                'authenticated' => false,
                'forced_logout' => true,
                'logout_kind' => $bloqueo['tipo'] === 'P' ? 'baja_permanente' : 'baja_cuenta',
                'message' => $bloqueo['tipo'] === 'P'
                    ? 'Tu cuenta ha sido dada de baja permanentemente. Se ha cerrado la sesión.'
                    : 'Tu cuenta ha sido dada de baja. Se ha cerrado la sesión.',
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode(['ok' => true, 'authenticated' => true], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Cierra la sesión por inactividad detectada en el cliente (POST + CSRF).
     */
    public function cerrarInactividad(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? '')) !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false], JSON_UNESCAPED_UNICODE);

            return;
        }
        require_once dirname(__DIR__, 2) . '/core/helpers/form_validacion.php';
        if (!csrf_validate_request()) {
            http_response_code(419);
            echo json_encode(['ok' => false, 'error' => 'csrf'], JSON_UNESCAPED_UNICODE);

            return;
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }
        session_destroy();
        echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
    }
}
