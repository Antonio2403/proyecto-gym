<?php

declare(strict_types=1);

require_once 'core/Controller.php';
require_once 'app/modelos/cliente.php';
require_once 'app/modelos/monitor.php';
require_once 'app/modelos/susbscripcion.php';
require_once 'app/modelos/actividades.php';
require_once 'app/modelos/fisioterapeuta.php';
require_once 'app/modelos/feedback.php';
require_once 'app/modelos/solicitud.php';

class AdminAjaxControlador extends Controller
{
    private function requireAdminJson(): void
    {
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            $this->sendJson(['ok' => false, 'error' => 'Acceso restringido'], 403);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function sendJson(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** @param array<string, string|null> $f */
    private function filtersFromGet(array $keys): array
    {
        $out = [];
        foreach ($keys as $k) {
            if (!isset($_GET[$k])) {
                continue;
            }
            $v = $_GET[$k];
            $out[$k] = is_string($v) ? $v : '';
        }

        return $out;
    }

    public function clientes(): void
    {
        $this->requireAdminJson();
        $p = gp_grid_pagination();
        $f = $this->filtersFromGet(['q', 'dni', 'nombre', 'email', 'telefono', 'metodo_pago']);
        $result = Cliente::buscarPaginado($p['page'], $p['per_page'], $f);
        $this->sendJson(array_merge(['ok' => true], $result));
    }

    public function monitores(): void
    {
        $this->requireAdminJson();
        $p = gp_grid_pagination();
        $f = $this->filtersFromGet(['q', 'dni', 'email', 'nombre', 'especialidad', 'disponibilidad']);
        $result = Monitor::buscarPaginado($p['page'], $p['per_page'], $f);
        $this->sendJson(array_merge(['ok' => true], $result));
    }

    public function subscripciones(): void
    {
        $this->requireAdminJson();
        $p = gp_grid_pagination();
        $f = $this->filtersFromGet(['q', 'nombre', 'precio_min', 'precio_max', 'duracion_min', 'duracion_max']);
        $result = Subscripcion::buscarPaginado($p['page'], $p['per_page'], $f);
        $this->sendJson(array_merge(['ok' => true], $result));
    }

    public function actividades(): void
    {
        $this->requireAdminJson();
        $p = gp_grid_pagination();
        $f = $this->filtersFromGet(['q', 'nombre', 'dia_semana', 'recurrente']);
        if (isset($_GET['sala_id']) && trim((string) $_GET['sala_id']) !== '') {
            $f['sala_id'] = max(0, (int) $_GET['sala_id']);
        }
        if (isset($_GET['monitor_id']) && trim((string) $_GET['monitor_id']) !== '') {
            $f['monitor_id'] = max(0, (int) $_GET['monitor_id']);
        }
        $result = Actividad::buscarPaginadoAdmin($p['page'], $p['per_page'], $f);
        $this->sendJson(array_merge(['ok' => true], $result));
    }

    public function fisioterapeutas(): void
    {
        $this->requireAdminJson();
        $p = gp_grid_pagination();
        $f = $this->filtersFromGet(['q', 'nombre', 'especialidad']);
        $result = Fisioterapeuta::buscarPaginado($p['page'], $p['per_page'], $f);
        $this->sendJson(array_merge(['ok' => true], $result));
    }

    public function feedback(): void
    {
        $this->requireAdminJson();
        $p = gp_grid_pagination();
        $f = $this->filtersFromGet(['q', 'nombre', 'email', 'asunto', 'fecha_desde', 'fecha_hasta']);
        $result = Feedback::buscarPaginado($p['page'], $p['per_page'], $f);
        $this->sendJson(array_merge(['ok' => true], $result));
    }

    /** estado: P pendientes, A aprobadas, R rechazadas (query string) */
    public function solicitudes(): void
    {
        $this->requireAdminJson();
        $p = gp_grid_pagination();
        $estado = strtoupper(trim((string) ($_GET['estado'] ?? 'P')));
        $f = $this->filtersFromGet(['q', 'tipo', 'monitor', 'fecha_desde', 'fecha_hasta']);
        $result = Solicitud::buscarPaginado($estado, $p['page'], $p['per_page'], $f);
        $this->sendJson(array_merge(['ok' => true, 'estado' => $estado], $result));
    }

    /** Firma para auto-actualizar el panel de tickets de cuenta (polling). */
    public function recuperacionTicketsFirma(): void
    {
        $this->requireAdminJson();
        require_once __DIR__ . '/../modelos/recuperacion_cuenta_ticket.php';
        try {
            $firma = RecuperacionCuentaTicket::firmaVistaAdminTickets(50);
        } catch (\Throwable $e) {
            error_log('[Spartum] recuperacionTicketsFirma: ' . $e->getMessage());
            $this->sendJson(['ok' => false, 'error' => 'No se pudo comprobar el estado.'], 500);
        }
        $this->sendJson(['ok' => true, 'firma' => $firma]);
    }
}
