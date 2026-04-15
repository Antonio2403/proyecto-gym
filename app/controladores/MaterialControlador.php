<?php

require_once "core/Controller.php";
require_once "app/modelos/material.php";

class MaterialControlador extends Controller
{
    public function index($sala_id)
    {
        $materiales = Material::obtenerPorSala($sala_id);
        $this->renderAdmin("materiales/verMateriales", ["materiales" => $materiales, "sala_id" => $sala_id]);
    }

    public function fromCrearMaterial($sala_id)
    {
        $this->renderAdmin("materiales/formMaterial", ["sala_id" => $sala_id]);
    }

    public function formEditarMaterial($sala_id, $material_id)
    {
        $material = Material::obtenerPorId($material_id);
        if ($material) {
            $this->renderAdmin("materiales/formEditarMaterial", ["material" => $material, "sala_id" => $sala_id]);
        } else {
            echo "Material no encontrado.";
        }
    }

    public function crear($sala_id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $estado = $_POST['estado'];

            if (Material::guardar($sala_id, $nombre, $estado)) {
                header("Location: /proyecto-gym/monitor/salas/" . $sala_id . "/materiales");
                exit();
            } else {
                error_log("Error al guardar el material.");
            }
        }
    }

    public function editar($sala_id, $material_id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $estado = $_POST['estado'];

            if (Material::actualizar($material_id, $nombre, $estado)) {
                header("Location: /proyecto-gym/monitor/salas/" . $sala_id . "/materiales");
                exit();
            } else {
                error_log("Error al actualizar el material.");
            }

            header("Location: /proyecto-gym/monitor/salas/" . $sala_id . "/materiales");
            exit();
        }
    }

    public function eliminar($sala_id, $material_id)
    {
        if (Material::eliminar($material_id)) {
            header("Location: /proyecto-gym/monitor/salas/" . $sala_id . "/materiales");
            exit();
        } else {
            echo "Error al eliminar la sala.";
        }
    }
}
