<?php

require_once 'core/Controller.php';
require_once 'app/modelos/usuario.php';

class CambioClaveControlador extends Controller
{
    public function formulario()
    {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Debes iniciar sesión'));
            exit;
        }

        $this->renderFrontend('frontend/cambiarClaveObligatoria', [
            'obligatoria' => !empty($_SESSION['password_must_change']),
        ]);
    }

    public function guardar()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/cuenta/cambiar-clave'));
            exit;
        }

        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login'));
            exit;
        }

        $uid = (int) $_SESSION['usuario_id'];
        $actual = (string) ($_POST['clave_actual'] ?? '');
        $nueva = (string) ($_POST['clave_nueva'] ?? '');
        $nueva2 = (string) ($_POST['clave_nueva2'] ?? '');

        if ($actual === '' || $nueva === '' || $nueva2 === '') {
            header('Location: ' . url('/cuenta/cambiar-clave') . '?error=' . rawurlencode('Completa todos los campos'));
            exit;
        }

        if ($nueva !== $nueva2) {
            header('Location: ' . url('/cuenta/cambiar-clave') . '?error=' . rawurlencode('La nueva contraseña y la confirmación no coinciden'));
            exit;
        }

        if (!fv_clave_fuerte($nueva)) {
            header(
                'Location: ' . url('/cuenta/cambiar-clave') . '?error=' . rawurlencode(
                    'La nueva contraseña debe tener al menos 16 caracteres e incluir mayúsculas, minúsculas, números y símbolos'
                )
            );
            exit;
        }

        $usuario = Usuario::obtenerPorId($uid);
        if (!$usuario || !password_verify($actual, $usuario->getClave())) {
            header('Location: ' . url('/cuenta/cambiar-clave') . '?error=' . rawurlencode('La contraseña actual no es correcta'));
            exit;
        }

        if (!Usuario::establecerClaveFuerte($uid, $nueva)) {
            header('Location: ' . url('/cuenta/cambiar-clave') . '?error=' . rawurlencode('No se pudo actualizar la contraseña'));
            exit;
        }

        unset($_SESSION['password_must_change']);
        $rol = (string) ($_SESSION['rol'] ?? 'cliente');
        $dest = '/inicioUsuario';
        if ($rol === 'admin') {
            $dest = '/inicioAdmin';
        } elseif ($rol === 'monitor') {
            $dest = '/inicioMonitor';
        } elseif ($rol === 'fisio') {
            $dest = '/fisio';
        }
        header('Location: ' . url($dest) . '?success=' . rawurlencode('Contraseña actualizada correctamente'));
        exit;
    }
}
