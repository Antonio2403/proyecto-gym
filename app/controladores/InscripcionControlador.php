<?php

require_once 'app/modelos/inscripcion.php';
require_once 'app/modelos/actividades.php';
require_once 'core/Controller.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



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
        $stmt = $conexion->prepare("SELECT clientes.id, usuarios.email FROM clientes join usuarios ON clientes.usuario_id = usuarios.id WHERE usuarios.id = ?");
        $stmt->execute([$usuario_id]);
        $cliente = $stmt->fetch();

        if (!$cliente) {
            die("No eres cliente");
        }

        $cliente_id = $cliente['id'];
        $cliente_email = $cliente['email'];

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
        $mail = new PHPMailer(true);

        try {
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host       = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['Mailtrap_USERNAME'];
            $mail->Password   = $_ENV['Mailtrap_PASSWORD'];
            $mail->Port       = 2525; // o 587

            // Remitente y destinatario
            $mail->setFrom('no-reply@tuapp.com', 'Tu App');
            $mail->addAddress($cliente_email);

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = 'Prueba desde PHP';
            $mail->Body    = '<b>Funciona correctamente</b>';
            $mail->AltBody = 'Funciona correctamente';

            $mail->send();
        } catch (Exception $e) {
            echo "Error: {$mail->ErrorInfo}";
        }

        header("Location:/proyecto-gym/usuario/actividades?success=1");
    }
}
