<?php

declare(strict_types=1);

require_once 'core/Controller.php';
require_once 'app/modelos/falta_asistencia.php';

class FaltaControlador extends Controller
{
    public function registro(): void
    {
        if (!isset($_SESSION['usuario_id']) || (string) ($_SESSION['rol'] ?? '') !== 'cliente') {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Acceso restringido'));
            exit;
        }
        $usuarioId = (int) $_SESSION['usuario_id'];
        $this->renderFrontend('frontend/faltas/registro', [
            'faltas' => FaltaAsistencia::deUsuario($usuarioId),
        ]);
    }
}
