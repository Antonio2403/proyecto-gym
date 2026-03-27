<?php

require_once 'core/Controller.php';
require_once 'app/modelos/susbscripcion.php';

class SubscripcionControlador extends Controller
{
    public function mostrarSubscripciones()
    {
        $subscripciones = Subscripcion::obtenerTodas();
        $this->view('gestionSubscripciones', $subscripciones);
    }
    public function formSubscripcion()
    {
        $this->view('formSubscripcion');
    }
    public function crearSubscripcion()
    {
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $duracion = $_POST['duracion'];

        Subscripcion::crear($nombre, $precio, $duracion);
        header('Location: gestionSubscripciones');
        exit();

    }

}

?>