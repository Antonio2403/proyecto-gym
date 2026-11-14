<?php

declare(strict_types=1);

require_once 'core/Controller.php';

use Spartum\Modelos\HistorialActividad;

final class ActividadUsuarioControlador extends Controller
{
    public function historial(): void
    {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Inicia sesión para consultar tu actividad.'));
            exit;
        }
        if ((string) ($_SESSION['rol'] ?? '') !== 'cliente') {
            header('Location: ' . url('/inicio'));
            exit;
        }

        $tipo = trim((string) ($_GET['tipo'] ?? ''));
        if (!in_array($tipo, ['', 'clases', 'planes', 'fisio'], true)) {
            $tipo = '';
        }
        $filtros = [
            'tipo' => $tipo,
            'desde' => $this->fechaValida($_GET['desde'] ?? null),
            'hasta' => $this->fechaValida($_GET['hasta'] ?? null),
        ];
        $resultado = HistorialActividad::porUsuario(
            (int) $_SESSION['usuario_id'],
            max(1, (int) ($_GET['pagina'] ?? 1)),
            10,
            $filtros
        );

        $this->renderFrontend('frontend/actividadUsuario', [
            'historial' => $resultado,
            'filtros' => $filtros,
        ]);
    }

    private function fechaValida(mixed $fecha): string
    {
        $fecha = trim((string) $fecha);

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) ? $fecha : '';
    }
}
