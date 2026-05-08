<?php

require_once 'core/Controller.php';
require_once 'app/modelos/fisioterapeuta.php';
require_once 'app/modelos/cita.php';

/**
 * Área profesional para usuarios con ficha de fisioterapeuta vinculada (rol sesión fisio).
 */
class FisioPanelControlador extends Controller
{
    /**
     * @return array{id:int,nombre:?string,especialidad:?string,usuario_id:?int}
     */
    private function exigirFisioPerfil(): array
    {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Debes iniciar sesión'));
            exit;
        }
        if (($_SESSION['rol'] ?? '') !== 'fisio') {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Acceso restringido'));
            exit;
        }
        $fisio = Fisioterapeuta::obtenerPorUsuarioId((int) $_SESSION['usuario_id']);
        if (!$fisio) {
            unset($_SESSION['rol']);
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Tu cuenta de fisioterapeuta no está vinculada correctamente'));
            exit;
        }

        return $fisio;
    }

    public function inicio()
    {
        $fisio = $this->exigirFisioPerfil();
        $this->renderFrontend('frontend/fisio/panel/inicio', [
            'fisio' => $fisio,
        ]);
    }

    public function citas()
    {
        $fisio = $this->exigirFisioPerfil();
        ['page' => $page, 'per_page' => $perPage] = gp_grid_pagination();

        $filtrosForm = [
            'estado' => trim((string) ($_GET['estado'] ?? '')),
            'fecha_desde' => trim((string) ($_GET['fecha_desde'] ?? '')),
            'fecha_hasta' => trim((string) ($_GET['fecha_hasta'] ?? '')),
            'motivo' => trim((string) ($_GET['motivo'] ?? '')),
            'cliente' => trim((string) ($_GET['cliente'] ?? '')),
            'solo_futuras' => isset($_GET['solo_futuras']) && (string) $_GET['solo_futuras'] === '1' ? '1' : '',
        ];

        $fModelo = [
            'estado' => gp_grid_str($filtrosForm['estado'] ?: null),
            'fecha_desde' => gp_grid_date_opt($filtrosForm['fecha_desde'] ?: null),
            'fecha_hasta' => gp_grid_date_opt($filtrosForm['fecha_hasta'] ?: null),
            'motivo' => gp_grid_str($filtrosForm['motivo'] ?: null),
            'cliente' => gp_grid_str($filtrosForm['cliente'] ?: null),
            'solo_futuras' => $filtrosForm['solo_futuras'],
        ];

        $resultado = Cita::buscarPorFisioterapeutaPaginado((int) $fisio['id'], $page, $perPage, $fModelo);

        $this->renderFrontend('frontend/fisio/panel/citas', [
            'fisio' => $fisio,
            'filtros' => $filtrosForm,
            'solo_confirmadas' => false,
            'citas_resultado' => $resultado,
        ]);
    }

    public function citasConfirmadas()
    {
        $fisio = $this->exigirFisioPerfil();
        ['page' => $page, 'per_page' => $perPage] = gp_grid_pagination();

        $filtrosForm = [
            'fecha_desde' => trim((string) ($_GET['fecha_desde'] ?? '')),
            'fecha_hasta' => trim((string) ($_GET['fecha_hasta'] ?? '')),
            'motivo' => trim((string) ($_GET['motivo'] ?? '')),
            'cliente' => trim((string) ($_GET['cliente'] ?? '')),
            'solo_futuras' => isset($_GET['solo_futuras']) && (string) $_GET['solo_futuras'] === '1' ? '1' : '',
        ];

        $fModelo = [
            'estado' => 'C',
            'fecha_desde' => gp_grid_date_opt($filtrosForm['fecha_desde'] ?: null),
            'fecha_hasta' => gp_grid_date_opt($filtrosForm['fecha_hasta'] ?: null),
            'motivo' => gp_grid_str($filtrosForm['motivo'] ?: null),
            'cliente' => gp_grid_str($filtrosForm['cliente'] ?: null),
            'solo_futuras' => $filtrosForm['solo_futuras'],
        ];

        $resultado = Cita::buscarPorFisioterapeutaPaginado((int) $fisio['id'], $page, $perPage, $fModelo);

        $this->renderFrontend('frontend/fisio/panel/citas', [
            'fisio' => $fisio,
            'filtros' => $filtrosForm,
            'solo_confirmadas' => true,
            'citas_resultado' => $resultado,
        ]);
    }
}
