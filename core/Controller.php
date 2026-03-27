<?php

class Controller {

    public function view($vista, $data = [])
    {
        extract($data);

        require_once "app/vistas/" . $vista . ".php";
    }

}