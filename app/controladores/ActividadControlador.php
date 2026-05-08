<?php

require_once 'core/Controller.php';
require_once 'app/modelos/actividades.php';
require_once 'app/modelos/sala.php';
require_once 'app/modelos/monitor.php';

class ActividadControlador extends Controller
{
    public function index()
    {
        $this->redirigirFisioFueraPortal();

        $actividades = Actividad::obtenerTodas();

        $semana = isset($_GET['semana']) ? (int) $_GET['semana'] : 0;
        if ($semana !== 0 && $semana !== 1) {
            $semana = 0;
        }

        $tz = new DateTimeZone(date_default_timezone_get());
        $monday = new DateTime('monday this week', $tz);
        if ($semana === 1) {
            $monday->modify('+1 week');
        }
        $sunday = clone $monday;
        $sunday->modify('+6 days');

        $weekLabel = sprintf(
            'Semana del %s al %s',
            $monday->format('d/m/Y'),
            $sunday->format('d/m/Y')
        );

        $weekStart = $monday->format('Y-m-d');
        $weekEnd = $sunday->format('Y-m-d');

        $actividades = array_values(array_filter($actividades, static function (array $act) use ($weekStart, $weekEnd): bool {
            $rec = (int) ($act['recurrente'] ?? 1);
            if ($rec === 1) {
                return true;
            }
            $fi = $act['fecha_inicio'] ?? '';
            if ($fi === '') {
                return false;
            }
            $d = substr((string) $fi, 0, 10);

            return $d >= $weekStart && $d <= $weekEnd;
        }));

        $ids = array_map(static fn (array $a): int => (int) $a['id'], $actividades);
        $inscritosPorActividad = Actividad::contarInscritosPorActividades($ids);

        $this->renderFrontend('frontend/actividades', [
            'actividades' => $actividades,
            'inscritos_por_actividad' => $inscritosPorActividad,
            'semana_offset' => $semana,
            'week_label' => $weekLabel,
            'schedule_week_monday' => $weekStart,
        ]);
    }

    public function gestionarActividades()
    {
        $salas = Sala::obtenerTodas();
        $monitoresFiltro = Monitor::obtenerTodos();
        $this->renderAdmin('admin/gestionarActividades', [
            'salas' => $salas,
            'monitoresFiltro' => $monitoresFiltro,
        ]);
    }

    public function formActividad()
    {
        $salas = Sala::obtenerTodas();
        $monitores = Monitor::obtenerTodos();
        $this->renderAdmin("admin/formActividades", ['salas' => $salas, 'monitores' => $monitores]);
    }

    public function formEditarActividad($id)
    {
        $salas = Sala::obtenerTodas();
        $monitores = Monitor::obtenerTodos();
        $actividad = Actividad::obtenerPorId($id);
        if (!$actividad) {
            header('Location: ' . url('/admin/gestionarActividades') . '?error=notfound');
            return;
        }
        $this->renderAdmin("admin/formEditarActividades", ['actividad' => $actividad, 'salas' => $salas, 'monitores' => $monitores]);
    }

    public function crearActividad()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/admin/actividades/crear'));
            return;
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
        $duracion = (int) ($_POST['duracion'] ?? 0);
        $dia_semana = (string) ($_POST['dia_semana'] ?? '');
        $hora_inicio = trim((string) ($_POST['hora_inicio'] ?? ''));
        $sala_id = (int) ($_POST['sala_id'] ?? 0);
        $monitor_id = (int) ($_POST['monitor_id'] ?? 0);

        $diasValidos = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
        if ($nombre === '' || $hora_inicio === '' || !in_array($dia_semana, $diasValidos, true)) {
            header('Location: ' . url('/admin/actividades/crear') . '?error=1');
            return;
        }

        if ($duracion < 1 || $duracion > 600 || $sala_id <= 0 || $monitor_id <= 0) {
            header('Location: ' . url('/admin/actividades/crear') . '?error=1');
            return;
        }

        // 👉 fecha base ficticia (solo para almacenar)
        $fecha_base = date("Y-m-d");

        $fecha_inicio = $fecha_base . " " . $hora_inicio . ":00";

        // 👉 sumar 1 hora automáticamente
        $fecha_fin = date("Y-m-d H:i:s", strtotime($fecha_inicio . " +1 hour"));

        if (Actividad::guardar(
            $nombre,
            $descripcion,
            $duracion,
            $monitor_id,
            $sala_id,
            $fecha_inicio,
            $fecha_fin,
            $dia_semana
        )) {
            header('Location: ' . url('/admin/gestionarActividades') . '?success=1');
        } else {
            echo "Error al crear la actividad.";
        }
    }
    public function editarActividad($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/admin/gestionarActividades'));
            exit;
        }

        $postId = (int) ($_POST['id'] ?? 0);
        $routeId = (int) $id;
        if ($postId !== $routeId || $postId <= 0) {
            header('Location: ' . url('/admin/gestionarActividades') . '?error=1');
            exit;
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
        $sala_id = (int) ($_POST['sala_id'] ?? 0);
        $monitor_id = (int) ($_POST['monitor_id'] ?? 0);

        if ($nombre === '' || $sala_id <= 0 || $monitor_id <= 0) {
            header('Location: ' . url('/admin/actividades/editar/' . $postId) . '?error=1');
            exit;
        }

        Actividad::actualizar($postId, $nombre, $descripcion, $sala_id, $monitor_id);

        header('Location: ' . url('/admin/gestionarActividades') . '?success=1');
        exit;
    }

    public function eliminarActividad($id)
    {
        if (Actividad::eliminar($id)) {
            header('Location: ' . url('/admin/gestionarActividades') . '?deleted=1');
        } else {
            echo "Error al eliminar la actividad.";
        }
    }
}
