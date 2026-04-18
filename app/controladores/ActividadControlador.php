<?php

require_once 'core/Controller.php';
require_once 'app/modelos/actividades.php';
require_once 'app/modelos/sala.php';
require_once 'app/modelos/monitor.php';

class ActividadControlador extends Controller
{
    public function index()
    {
        $this->renderFrontend("frontend/actividades");
    }

    public function gestionarActividades()
    {
        $actividades = Actividad::obtenerTodas();
        $this->renderAdmin("admin/gestionarActividades", ['actividades' => $actividades]);
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
            header("Location: /admin/gestionarActividades?error=notfound");
            return;
        }
        $this->renderAdmin("admin/formEditarActividades", ['actividad' => $actividad, 'salas' => $salas, 'monitores' => $monitores]);
    }

    public function crearActividad()
    {
        $nombre = $_POST['nombre'] ?? null;
        $descripcion = $_POST['descripcion'] ?? null;
        $sala_id = $_POST['sala_id'] ?? null;
        $monitor_id = $_POST['monitor_id'] ?? null;

        if (!$nombre || !$sala_id || !$monitor_id) {
            header("Location: /admin/actividades/crear?error=1");
            return;
        }

        if (Actividad::guardar($nombre, $descripcion, $sala_id, $monitor_id)) {
            header("Location: /proyecto-gym/admin/gestionarActividades?success=1");
        } else {
            echo "Error al crear la actividad.";
        }
    }
    public function editarActividad($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $sala_id = $_POST['sala_id'];
            $monitor_id = $_POST['monitor_id'];

            Actividad::actualizar($id, $nombre, $descripcion, $sala_id, $monitor_id);

            header("Location: /proyecto-gym/admin/gestionarActividades?success=1");
            exit;
        }
    }

    public function eliminarActividad($id)
    {
        if (Actividad::eliminar($id)) {
            header("Location: /proyecto-gym/admin/gestionarActividades?deleted=1");
        } else {
            echo "Error al eliminar la actividad.";
        }
    }
}
