<?php

require_once "core/Controller.php";

class InicioControlador extends Controller {

    public function index()
    {
        $this->renderFrontend("frontend/inicio");
    }

    public function inicioAdmin(){
        $this->renderAdmin("admin/inicioAdmin");
    }

    public function inicioUsuario(){
        $this->renderFrontend("frontend/inicioUsuario");
    }

    public function inicioMonitor(){
        $this->renderFrontend("monitor/inicioMonitor");
    }

}