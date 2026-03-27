<?php

require_once "core/Controller.php";
require_once "app/modelos/admin.php";
require_once "app/modelos/solicitud.php";

class AdminControlador extends Controller
{
    public function registrarMonitor()
    {
        $this->view("registrarMonitor");
    }
    public function crearMonitor()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $datos = $_POST;

            if (Admin::crearMonitor($datos)) {
                header("Location: /proyecto-gym/inicioAdmin");
                exit;
            } else {
                echo "Error al crear monitor";
            }
        }
    }
    public function verSolicitudes()
    {
        $solicitudes = Solicitud::obtenerPendientes();
        $this->view("verSolicitudes", $solicitudes);
    }

    public function verSolicitudesAprobadas()
    {
        $solicitudes = Solicitud::obtenerAprobadas();
        $this->view("verSolicitudesAprobadas", $solicitudes);
    }

    public function verSolicitudesRechazadas()
    {
        $solicitudes = Solicitud::obtenerRechazadas();
        $this->view("verSolicitudesRechazadas", $solicitudes);
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
