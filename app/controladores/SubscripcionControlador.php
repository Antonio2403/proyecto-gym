<?php

require_once 'core/Controller.php';
require_once 'app/modelos/susbscripcion.php';

class SubscripcionControlador extends Controller
{
    public function mostrarSubscripciones()
    {
        $subscripciones = Subscripcion::obtenerTodas();
        $this->renderAdmin('admin/gestionSubscripciones', ['subscripciones' => $subscripciones]);
    }

    public function formSubscripcion()
    {
        $this->renderAdmin('admin/formSubscripcion');
    }

    public function formEditarSubscripcion()
    {
        $id = $_GET['id'];
        $subscripcion = Subscripcion::obtenerPorId($id);
        $this->renderAdmin('admin/formEditarSubscripcion', ['subscripcion' => $subscripcion]);
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

    public function eliminarSubscripcion()
    {
        $id = $_POST['id'];
        Subscripcion::eliminar($id);
        header('Location: gestionSubscripciones');
        exit();
    }

    public function editarSubscripcion()
    {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $duracion = $_POST['duracion'];

        Subscripcion::actualizar($id, $nombre, $precio, $duracion);
        header('Location: gestionSubscripciones');
        exit();
    }
}
