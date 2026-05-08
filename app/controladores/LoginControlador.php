<?php

require_once "core/Controller.php";
require_once "app/modelos/usuario.php";


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

        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $clave = (string) ($_POST['clave'] ?? '');

        if ($email === '' || $clave === '') {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Email y contraseña son obligatorios'));
            exit;
        }

        if (!fv_email_valido($email)) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Email no válido'));
            exit;
        }

        $usuario = Usuario::obtenerPorEmail($email);

        if (!$usuario || !password_verify($clave, $usuario->getClave())) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Email o contraseña incorrectos'));
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

        $_SESSION['usuario_id'] = $usuario->getId();
        $_SESSION['email'] = $usuario->getEmail();
        $_SESSION['nombre'] = $usuario->getNombre();

        $_SESSION['rol'] = 'cliente';

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
        session_destroy();
        header('Location: ' . url('/login'));
        exit;
    }
}
