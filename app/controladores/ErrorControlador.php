<?php

require_once "core/Controller.php";

class ErrorControlador extends Controller {
    public function error404() {
        header("HTTP/1.0 404 Not Found");
        $this->view("error404");
    }
}

?>