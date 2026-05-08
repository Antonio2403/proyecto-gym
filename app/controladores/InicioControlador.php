<?php

require_once "core/Controller.php";

class InicioControlador extends Controller {

    public function index()
    {
        $this->renderFrontend("frontend/inicio");
    }

    public function quienesSomos()
    {
        $this->renderFrontend('frontend/quienesSomos');
    }

    public function inicioAdmin(){
        $this->renderAdmin("admin/inicioAdmin");
    }

    public function inicioUsuario(){
        if (($_SESSION['rol'] ?? '') === 'fisio') {
            header('Location: ' . url('/fisio'));
            exit;
        }
        $this->renderFrontend("frontend/inicioUsuario");
    }

    public function inicioMonitor()
    {
        $rol = $_SESSION['rol'] ?? '';
        if ($rol === 'admin') {
            header('Location: ' . url('/admin'));
            exit;
        }
        if ($rol !== 'monitor') {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Acceso restringido'));
            exit;
        }

        $this->renderAdmin('monitor/inicioMonitor');
    }
}
