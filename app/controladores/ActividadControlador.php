<?php

require_once 'core/Controller.php';
require_once 'app/modelos/actividades.php';
require_once 'app/modelos/sala.php';
require_once 'app/modelos/monitor.php';
require_once 'app/modelos/inscripcion.php';
require_once 'app/modelos/cliente.php';
require_once 'app/modelos/cliente_subscripcion.php';

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

        $offsetDia = ['L' => 0, 'M' => 1, 'X' => 2, 'J' => 3, 'V' => 4, 'S' => 5, 'D' => 6];
        $inscritosPorCelda = [];
        foreach ($actividades as $act) {
            $id = (int) $act['id'];
            $dias = $act['dias'] ?? [];
            $rec = (int) ($act['recurrente'] ?? 1);
            if ($rec === 1) {
                foreach ($dias as $diaLetra) {
                    $delta = $offsetDia[$diaLetra] ?? 0;
                    $fechaCelda = (new DateTimeImmutable($weekStart))->modify('+' . $delta . ' days')->format('Y-m-d');
                    $key = $id . '_' . $fechaCelda;
                    $inscritosPorCelda[$key] = Inscripcion::contarInscritosSesion($id, $fechaCelda);
                }
            } elseif (!empty($act['fecha_inicio'])) {
                $fechaCelda = substr((string) $act['fecha_inicio'], 0, 10);
                if ($fechaCelda >= $weekStart && $fechaCelda <= $weekEnd) {
                    $key = $id . '_' . $fechaCelda;
                    $inscritosPorCelda[$key] = Inscripcion::contarInscritosSesion($id, $fechaCelda);
                }
            }
        }

        $usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);
        $rol = (string) ($_SESSION['rol'] ?? '');
        $clienteId = $usuarioId > 0 ? Cliente::IdClientePorUsuarioId($usuarioId) : null;
        $tienePlan = $usuarioId > 0 && ClienteSubscripcion::tieneSuscripcionActivaPorUsuarioId($usuarioId);
        $esClienteSocio = ($rol === 'cliente' && $clienteId !== null);

        $cupoResumen = null;
        $inscritoMap = [];
        $puedeMasReservaSemana = true;
        if ($clienteId) {
            if ($tienePlan) {
                $cupoResumen = ClienteSubscripcion::cupoReservasSemana((int) $clienteId, $weekStart);
                if (($cupoResumen['max_semana'] ?? 0) > 0) {
                    $cupoResumen['restante'] = max(0, (int) $cupoResumen['max_semana'] - (int) $cupoResumen['usado']);
                } else {
                    $cupoResumen['restante'] = null;
                }
                $inscritoMap = Inscripcion::mapaInscripcionesClienteEnSemana((int) $clienteId, $weekStart, $weekEnd);
                $puedeMasReservaSemana = ClienteSubscripcion::puedeNuevaReservaEsaSemana((int) $clienteId, $weekStart);
            }
        }

        $this->renderFrontend('frontend/actividades', [
            'actividades' => $actividades,
            'inscritos_por_celda' => $inscritosPorCelda,
            'semana_offset' => $semana,
            'week_label' => $weekLabel,
            'schedule_week_monday' => $weekStart,
            'schedule_week_end' => $weekEnd,
            'tiene_plan_activo' => $tienePlan,
            'es_cliente_socio' => $esClienteSocio,
            'cupo_resumen_semana' => $cupoResumen,
            'inscrito_por_sesion' => $inscritoMap,
            'puede_mas_reserva_semana' => $puedeMasReservaSemana,
        ]);
    }

    public function gestionarActividades()
    {
        $this->requireRole('admin');
        $salas = Sala::obtenerTodas();
        $monitoresFiltro = Monitor::obtenerTodos();
        $this->renderAdmin('admin/gestionarActividades', [
            'salas' => $salas,
            'monitoresFiltro' => $monitoresFiltro,
        ]);
    }

    public function formActividad()
    {
        $this->requireRole('admin');
        $salas = Sala::obtenerTodas();
        $monitores = Monitor::obtenerTodos();
        $this->renderAdmin("admin/formActividades", ['salas' => $salas, 'monitores' => $monitores]);
    }

    public function formEditarActividad($id)
    {
        $this->requireRole('admin');
        $salas = Sala::obtenerTodas();
        $monitores = Monitor::obtenerTodos();
        $actividad = Actividad::obtenerPorId($id);
        if (!$actividad) {
            header('Location: ' . url('/admin/gestionarActividades') . '?error=notfound');
            return;
        }
        $diasActividad = Actividad::diasParaActividadId((int) $id);
        $this->renderAdmin("admin/formEditarActividades", [
            'actividad' => $actividad,
            'dias_actividad' => $diasActividad,
            'salas' => $salas,
            'monitores' => $monitores,
        ]);
    }

    public function crearActividad()
    {
        $this->requireRole('admin');
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/admin/actividades/crear'));
            return;
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
        $duracion = (int) ($_POST['duracion'] ?? 0);
        $rawDias = $_POST['dias_semana'] ?? [];
        if (!is_array($rawDias)) {
            $rawDias = [];
        }
        $diasValidos = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
        $diasSel = array_values(array_intersect($diasValidos, array_map('strtoupper', array_map('strval', $rawDias))));
        $hora_inicio = trim((string) ($_POST['hora_inicio'] ?? ''));
        $sala_id = (int) ($_POST['sala_id'] ?? 0);
        $monitor_id = (int) ($_POST['monitor_id'] ?? 0);
        $recurrente = isset($_POST['recurrente']) ? 1 : 0;

        if ($nombre === '' || $hora_inicio === '' || $diasSel === []) {
            header('Location: ' . url('/admin/actividades/crear') . '?error=1');
            return;
        }

        if ($duracion < 1 || $duracion > 600 || $sala_id <= 0 || $monitor_id <= 0) {
            header('Location: ' . url('/admin/actividades/crear') . '?error=1');
            return;
        }

        $fecha_base = date('Y-m-d');
        $fecha_inicio = $fecha_base . ' ' . $hora_inicio . ':00';
        $fecha_fin = date('Y-m-d H:i:s', strtotime($fecha_inicio . ' +' . $duracion . ' minutes'));

        $primero = $diasSel[0];
        $newId = Actividad::guardar(
            $nombre,
            $descripcion,
            $duracion,
            $monitor_id,
            $sala_id,
            $fecha_inicio,
            $fecha_fin,
            $primero,
            $diasSel
        );
        if ($newId !== false) {
            if ($recurrente === 0) {
                $db = BasedeDatos::Conectar();
                $db->prepare('UPDATE actividades SET recurrente = 0 WHERE id = ?')->execute([(int) $newId]);
            }
            header('Location: ' . url('/admin/gestionarActividades') . '?success=1');
            exit;
        } else {
            header('Location: ' . url('/admin/actividades/crear') . '?error=' . rawurlencode('No se pudo crear la actividad'));
            exit;
        }
    }
    public function editarActividad($id)
    {
        $this->requireRole('admin');
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
        $duracion = (int) ($_POST['duracion'] ?? 0);
        $hora_inicio = trim((string) ($_POST['hora_inicio'] ?? ''));
        $recurrente = isset($_POST['recurrente']) ? 1 : 0;
        $rawDias = $_POST['dias_semana'] ?? [];
        if (!is_array($rawDias)) {
            $rawDias = [];
        }
        $diasValidos = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
        $diasSel = array_values(array_intersect($diasValidos, array_map('strtoupper', array_map('strval', $rawDias))));

        if ($nombre === '' || $sala_id <= 0 || $monitor_id <= 0 || $hora_inicio === '' || $diasSel === [] || $duracion < 1) {
            header('Location: ' . url('/admin/actividades/editar/' . $postId) . '?error=1');
            exit;
        }

        if (!Actividad::actualizarHorario($postId, $nombre, $descripcion, $sala_id, $monitor_id, $duracion, $hora_inicio, $recurrente, $diasSel)) {
            header('Location: ' . url('/admin/actividades/editar/' . $postId) . '?error=1');
            exit;
        }

        header('Location: ' . url('/admin/gestionarActividades') . '?success=1');
        exit;
    }

    public function eliminarActividad($id)
    {
        $this->requireRole('admin');
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/admin/gestionarActividades'));
            exit;
        }
        if (Actividad::eliminar($id)) {
            header('Location: ' . url('/admin/gestionarActividades') . '?deleted=1');
        } else {
            header('Location: ' . url('/admin/gestionarActividades') . '?error=' . rawurlencode('No se pudo eliminar la actividad'));
        }
        exit;
    }
}
