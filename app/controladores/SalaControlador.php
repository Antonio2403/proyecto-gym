<?php

require_once "core/Controller.php";
require_once "app/modelos/sala.php";
require_once "app/modelos/material.php";

Class SalaControlador extends Controller
{
    public function index()
    {
        $this->requireRole(['admin', 'monitor']);
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
        $this->requireRole(['admin', 'monitor']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/monitor/salas/crear'));
            exit;
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $capacidad = (int) ($_POST['capacidad'] ?? 0);
        $disponibilidad = trim((string) ($_POST['disponibilidad'] ?? ''));

        if ($nombre === '' || $capacidad < 1 || $capacidad > 10000 || $disponibilidad === '') {
            header('Location: ' . url('/monitor/salas/crear') . '?error=1');
            exit;
        }

        if (Sala::crear($nombre, $capacidad, $disponibilidad)) {
            header('Location: ' . url('/monitor/verSalas'));
            exit;
        }

        header('Location: ' . url('/monitor/salas/crear') . '?error=1');
        exit;
    }
    public function formCrearSala()
    {
        $this->requireRole(['admin', 'monitor']);
        $this->renderAdmin("salas/formSala");
    }

    public function formEditarSala($id)
    {
        $this->requireRole(['admin', 'monitor']);
        $sala = Sala::obtenerPorId($id);
        if ($sala) {
            $this->renderAdmin("salas/formEditarSala", ["sala" => $sala]);
        } else {
            echo "Sala no encontrada.";
        }
    }

    public function eliminar($id)
    {
        $this->requireRole(['admin', 'monitor']);
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/monitor/verSalas'));
            exit;
        }
        if (Sala::eliminar($id)) {
            header('Location: ' . url('/monitor/verSalas'));
            exit();
        } else {
            header('Location: ' . url('/monitor/verSalas') . '?error=' . rawurlencode('No se pudo eliminar la sala'));
            exit();
        }
    }

    public function actualizar($id)
    {
        $this->requireRole(['admin', 'monitor']);
        $id = (int) $id;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/monitor/verSalas'));
            exit;
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $capacidad = (int) ($_POST['capacidad'] ?? 0);
        $disponibilidad = trim((string) ($_POST['disponibilidad'] ?? ''));

        if ($nombre === '' || $capacidad < 1 || $capacidad > 10000 || $disponibilidad === '') {
            header('Location: ' . url('/monitor/salas/editar/' . $id) . '?error=1');
            exit;
        }

        if (Sala::actualizar($id, $nombre, $capacidad, $disponibilidad)) {
            header('Location: ' . url('/monitor/verSalas'));
            exit;
        }

        header('Location: ' . url('/monitor/salas/editar/' . $id) . '?error=1');
        exit;
    }
}



?>