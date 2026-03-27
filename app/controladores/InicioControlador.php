<?php

require_once "core/Controller.php";

class InicioControlador extends Controller {

    public function index()
    {
        $this->view("inicio");
    }

    public function inicioAdmin(){
        $this->view("inicioAdmin");
    }

    public function inicioUsuario(){
        $this->view("inicioUsuario");
    }

    public function inicioMonitor(){
        $this->view("inicioMonitor");
    }

}