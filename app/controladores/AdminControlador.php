<?php

require_once "core/Controller.php";
require_once "app/modelos/admin.php";
require_once "app/modelos/solicitud.php";
require_once "app/modelos/monitor.php";

class AdminControlador extends Controller
{
    public function verMonitores()
    {
        $monitores = Monitor::obtenerTodos();
        $this->renderAdmin("admin/verMonitores", ['monitores' => $monitores]);
    }

    public function formEditarMonitor($id)
    {
        $monitor = Monitor::obtenerPorId($id);
        if ($monitor) {
            $this->renderAdmin("admin/formEditarMonitor", ["monitor" => $monitor]);
        } else {
            echo "Monitor no encontrado.";
        }
    }

    public function editarMonitor()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id = $_POST['id'];
            $DNI = $_POST['DNI'];
            $nombre = $_POST['nombre'];
            $apellido1 = $_POST['apellido1'];
            $apellido2 = $_POST['apellido2'];
            $email = $_POST['email'];
            $clave = $_POST['clave'] ?? null;
            $telefono = $_POST['telefono'];
            $especialidad = $_POST['especialidad'];
            $disponibilidad = $_POST['disponibilidad'];

            if (Monitor::actualizar(
                $id,
                $DNI,
                $nombre,
                $apellido1,
                $apellido2,
                $email,
                $clave,
                $telefono,
                $especialidad,
                $disponibilidad
            )) {
                header("Location: /proyecto-gym/admin/verMonitores");
                exit;
            } else {
                echo "Error al editar monitor";
            }
        }
    }

    public function registrarMonitor()
    {
        $this->renderAdmin("admin/registrarMonitor");
    }
    public function crearMonitor()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $datos = $_POST;

            if (Admin::crearMonitor($datos)) {
                header("Location: /proyecto-gym/admin/verMonitores");
                exit;
            } else {
                echo "Error al crear monitor";
            }
        }
    }
    public function verSolicitudes()
    {
        $solicitudes = Solicitud::obtenerPendientes();
        $this->renderAdmin("admin/verSolicitudes", ['solicitudes' => $solicitudes]);
    }

    public function verSolicitudesAprobadas()
    {
        $solicitudes = Solicitud::obtenerAprobadas();
        $this->renderAdmin("admin/verSolicitudesAprobadas", ['solicitudes' => $solicitudes]);
    }

    public function verSolicitudesRechazadas()
    {
        $solicitudes = Solicitud::obtenerRechazadas();
        $this->renderAdmin("admin/verSolicitudesRechazadas", ['solicitudes' => $solicitudes]);
    }

    public function aprobarSolicitud()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_admin = $_SESSION['usuario_id'];
            $id = $_POST['id'];
            $estado = $_POST['estado'];

            if (Solicitud::cambiarEstado($id, $estado, $id_admin)) {
                echo "La solicitud ha sido " . ($estado === 'A' ? "aprobada" : "rechazada");
                exit;
            } else {
                echo "Error al actualizar solicitud";
            }
        }
    }
}
