<?php

require_once 'core/Controller.php';
require_once 'app/modelos/monitor.php';
require_once 'app/modelos/solicitud.php';
require_once 'app/modelos/actividades.php';
require_once 'app/modelos/inscripcion.php';

class MonitorControlador extends Controller
{
    private function requireMonitor(): void
    {
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'monitor') {
            header('Location: ' . url('/login') . '?error=' . urlencode('Acceso restringido'));
            exit;
        }
    }

    public function verMonitorSolicitudes()
    {
        $this->requireMonitor();
        $pendientes = Solicitud::obtenerPendientes();
        $aprobadas = array_slice(Solicitud::obtenerAprobadas(), 0, 12);
        $rechazadas = array_slice(Solicitud::obtenerRechazadas(), 0, 12);
        $this->renderAdmin('monitor/verMonitorSolicitudes', [
            'pendientes' => $pendientes,
            'aprobadas' => $aprobadas,
            'rechazadas' => $rechazadas,
        ]);
    }

    public function verMisSolicitudes()
    {
        $this->requireMonitor();
        $monitor_id = $_SESSION['usuario_id'];
        $solicitudes = Solicitud::obtenerPorMonitor($monitor_id);
        $this->renderAdmin('monitor/verMisSolicitudes', ['solicitudes' => $solicitudes]);
    }

    public function formSolicitud()
    {
        $this->requireMonitor();
        $this->renderAdmin('monitor/formSolicitud');
    }

    public function misClases()
    {
        $this->requireMonitor();
        $monitorTablaId = Monitor::obtenerIdPorUsuarioId((int) $_SESSION['usuario_id']);
        if ($monitorTablaId === null) {
            header('Location: ' . url('/inicioMonitor') . '?error=' . urlencode('No se encontró el perfil de monitor.'));
            exit;
        }

        $pag = gp_grid_pagination();
        $filtros = [
            'q' => gp_grid_str($_GET['q'] ?? null),
            'actividad' => gp_grid_str($_GET['actividad'] ?? null),
            'sala' => gp_grid_str($_GET['sala'] ?? null),
            'cliente' => gp_grid_str($_GET['cliente'] ?? null),
            'email' => gp_grid_str($_GET['email'] ?? null),
            'fecha_desde' => gp_grid_str($_GET['fecha_desde'] ?? null),
            'fecha_hasta' => gp_grid_str($_GET['fecha_hasta'] ?? null),
            'dia_semana' => gp_grid_str($_GET['dia_semana'] ?? null),
        ];

        $resultado = Inscripcion::buscarSesionesMonitorPaginado(
            $monitorTablaId,
            $pag['page'],
            $pag['per_page'],
            $filtros
        );

        $tz = new DateTimeZone(date_default_timezone_get());
        $hoy = (new DateTimeImmutable('today', $tz))->format('Y-m-d');
        $defHasta = (new DateTimeImmutable('today', $tz))->modify('+13 days')->format('Y-m-d');

        $this->renderAdmin('monitor/misClases', [
            'sesiones' => $resultado['rows'],
            'total' => $resultado['total'],
            'page' => $resultado['page'],
            'per_page' => $resultado['per_page'],
            'total_pages' => $resultado['total_pages'],
            'filtros' => $filtros,
            'salas_filtro' => Inscripcion::salasDistintasMonitor($monitorTablaId),
            'fecha_def_desde' => $hoy,
            'fecha_def_hasta' => $defHasta,
        ]);
    }

    public function crearSolicitud()
    {
        $this->requireMonitor();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $tipo = trim($_POST['tipo'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');

            if ($tipo === '') {
                header('Location: ' . url('/monitor/formSolicitud') . '?error=' . urlencode('El tipo de solicitud es obligatorio'));
                exit;
            }

            if (mb_strlen($tipo) > 120) {
                header('Location: ' . url('/monitor/formSolicitud') . '?error=' . urlencode('El tipo es demasiado largo'));
                exit;
            }

            if (mb_strlen($descripcion) > 4000) {
                header('Location: ' . url('/monitor/formSolicitud') . '?error=' . urlencode('La descripción no puede superar 4000 caracteres'));
                exit;
            }

            $monitorTablaId = Monitor::obtenerIdPorUsuarioId((int) $_SESSION['usuario_id']);
            if ($monitorTablaId === null) {
                header('Location: ' . url('/monitor/formSolicitud') . '?error=' . urlencode('No se encontró el perfil de monitor.'));
                exit;
            }

            $descGuardar = $descripcion !== '' ? $descripcion : null;
            if (Solicitud::crear($monitorTablaId, $tipo, $descGuardar)) {
                header('Location: ' . url('/monitor/verMisSolicitudes') . '?success=' . urlencode('Solicitud enviada correctamente.'));
                exit;
            }

            header('Location: ' . url('/monitor/formSolicitud') . '?error=' . urlencode('No se pudo registrar la solicitud.'));
            exit;
        }
    }
}
