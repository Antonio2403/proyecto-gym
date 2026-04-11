<?php

require_once "core/Controller.php";
require_once "app/modelos/monitor.php";
require_once "app/modelos/solicitud.php";

class MonitorControlador extends Controller
{

    public function verMonitorSolicitudes()
    {
        $this->renderAdmin("monitor/verMonitorSolicitudes");
    }

    public function verMisSolicitudes()
    {
        $monitor_id = $_SESSION['usuario_id'];
        $solicitudes = Solicitud::obtenerPorMonitor($monitor_id);
        $this->renderAdmin("monitor/verMisSolicitudes", ['solicitudes' => $solicitudes]);
    }

    public function formSolicitud()
    {
        $this->renderAdmin("monitor/formSolicitud");
    }

    public function crearSolicitud()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $tipo = $_POST['tipo'];
            $monitor_id = $_SESSION['usuario_id'];

            if (Solicitud::crear($monitor_id, $tipo)) {
                echo "Solicitud enviada";
            } else {
                echo "Error";
            }
        }
    }
}
