<?php

require_once "core/Controller.php";
require_once "app/modelos/material.php";

class MaterialControlador extends Controller
{
    public function index($sala_id)
    {
        $this->requireRole(['admin', 'monitor']);
        $materiales = Material::obtenerPorSala($sala_id);
        $this->renderAdmin("materiales/verMateriales", ["materiales" => $materiales, "sala_id" => $sala_id]);
    }

    public function fromCrearMaterial($sala_id)
    {
        $this->requireRole(['admin', 'monitor']);
        $this->renderAdmin("materiales/formMaterial", ["sala_id" => $sala_id]);
    }

    public function formEditarMaterial($sala_id, $material_id)
    {
        $this->requireRole(['admin', 'monitor']);
        $material = Material::obtenerPorId($material_id);
        if ($material) {
            $this->renderAdmin("materiales/formEditarMaterial", ["material" => $material, "sala_id" => $sala_id]);
        } else {
            echo "Material no encontrado.";
        }
    }

    public function crear($sala_id)
    {
        $this->requireRole(['admin', 'monitor']);
        $sala_id = (int) $sala_id;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/monitor/salas/' . $sala_id . '/materiales/crear'));
            exit;
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $estado = (string) ($_POST['estado'] ?? '');

        if ($nombre === '' || !in_array($estado, ['B', 'M'], true)) {
            header('Location: ' . url('/monitor/salas/' . $sala_id . '/materiales/crear') . '?error=1');
            exit;
        }

        if (Material::guardar($sala_id, $nombre, $estado)) {
            header('Location: ' . url('/monitor/salas/' . $sala_id . '/materiales'));
            exit;
        }

        header('Location: ' . url('/monitor/salas/' . $sala_id . '/materiales/crear') . '?error=1');
        exit;
    }

    public function editar($sala_id, $material_id)
    {
        $this->requireRole(['admin', 'monitor']);
        $sala_id = (int) $sala_id;
        $material_id = (int) $material_id;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/monitor/salas/' . $sala_id . '/materiales'));
            exit;
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $estado = (string) ($_POST['estado'] ?? '');

        if ($nombre === '' || !in_array($estado, ['B', 'M'], true)) {
            header('Location: ' . url('/monitor/salas/' . $sala_id . '/materiales/editar/' . $material_id) . '?error=1');
            exit;
        }

        if (Material::actualizar($material_id, $nombre, $estado)) {
            header('Location: ' . url('/monitor/salas/' . $sala_id . '/materiales'));
            exit;
        }

        header('Location: ' . url('/monitor/salas/' . $sala_id . '/materiales/editar/' . $material_id) . '?error=1');
        exit;
    }

    public function eliminar($sala_id, $material_id)
    {
        $this->requireRole(['admin', 'monitor']);
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/monitor/salas/' . (int) $sala_id . '/materiales'));
            exit;
        }
        if (Material::eliminar($material_id)) {
            header('Location: ' . url('/monitor/salas/' . $sala_id . '/materiales'));
            exit();
        } else {
            header('Location: ' . url('/monitor/salas/' . $sala_id . '/materiales') . '?error=' . rawurlencode('No se pudo eliminar el material'));
            exit();
        }
    }
}
