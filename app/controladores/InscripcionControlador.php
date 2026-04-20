<?php

require_once 'app/modelos/inscripcion.php';
require_once 'app/modelos/actividades.php';
require_once 'core/Controller.php';

class InscripcionControlador extends Controller
{
    public function misIncripciones()
    {
        $inscripciones = Inscripcion::obtenerInscripciones();
        $this->renderFrontend("frontend/verMisActividades", ['inscripciones' => $inscripciones]);
    }

    public function cancelar()
{
    $id = $_POST['inscripcion_id'];

    if (Inscripcion::cancelar($id)) {
        header("Location: /proyecto-gym/usuario/inscripciones/mis-inscripciones?success=1");
    } else {
        header("Location: /proyecto-gym/usuario/inscripciones/mis-inscripciones?error=1");
    }
}

    public function inscribirse()
    {
        $actividad_id = $_POST['actividad_id'];
        $usuario_id = $_SESSION['usuario_id'];

        $conexion = BasedeDatos::Conectar();

        // Obtener cliente
        $stmt = $conexion->prepare("SELECT id FROM clientes WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        $cliente = $stmt->fetch();

        if (!$cliente) {
            die("No eres cliente");
        }

        $cliente_id = $cliente['id'];

        // Evitar duplicados
        if (Inscripcion::yaInscrito($cliente_id, $actividad_id)) {
            header("Location: /proyecto-gym/horario?error=duplicado");
            exit;
        }

        // Obtener capacidad
        $actividad = Actividad::obtenerPorId($actividad_id);
        $inscritos = Inscripcion::contarInscritos($actividad_id);

        // Clase llena
        if ($inscritos >= $actividad['plazas']) {
            header("Location:/proyecto-gym/usuario/actividades?error=completo");
            exit;
        }

        // Insertar
        Inscripcion::inscribir($cliente_id, $actividad_id);

        header("Location:/proyecto-gym/usuario/actividades?success=1");
    }
}