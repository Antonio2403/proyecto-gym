<?php

require_once "core/Controller.php";
require_once "app/modelos/sala.php";
require_once "app/modelos/material.php";

Class SalaControlador extends Controller
{
    public function index()
    {
        $salas = Sala::obtenerTodas();
        $salasConMateriales = [];
        
        foreach ($salas as $sala) {
            $materiales = Material::obtenerPorSala($sala['id']);
            $sala['nmateriales'] = count($materiales);
            $salasConMateriales[] = $sala;
        }
        
        $this->renderAdmin("salas/verSalas", ["salas" => $salasConMateriales]);
    }

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $capacidad = $_POST['capacidad'];
            $disponibilidad = $_POST['disponibilidad'];

            if (Sala::crear($nombre, $capacidad, $disponibilidad)) {
                header("Location: /proyecto-gym/monitor/verSalas");
                exit();
            } else {
                echo "Error al crear la sala.";
            }
        }
    }
    public function formCrearSala()
    {
        $this->renderAdmin("salas/formSala");
    }

    public function formEditarSala($id)
    {
        $sala = Sala::obtenerPorId($id);
        if ($sala) {
            $this->renderAdmin("salas/formEditarSala", ["sala" => $sala]);
        } else {
            echo "Sala no encontrada.";
        }
    }

    public function eliminar($id)
    {
        if (Sala::eliminar($id)) {
            header("Location: /proyecto-gym/monitor/verSalas");
            exit();
        } else {
            echo "Error al eliminar la sala.";
        }
    }

    public function actualizar($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $capacidad = $_POST['capacidad'];
            $disponibilidad = $_POST['disponibilidad'];

            if (Sala::actualizar($id, $nombre, $capacidad, $disponibilidad)) {
                header("Location: /proyecto-gym/monitor/verSalas");
                exit();
            } else {
                echo "Error al actualizar la sala.";
            }
        } else {
            // Aquí podrías cargar los datos actuales de la sala para mostrar en el formulario
            // y luego renderizar la vista de actualización
        }
    }
}



?>