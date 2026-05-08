<?php

require_once 'core/Controller.php';
require_once 'app/modelos/feedback.php';

class FeedbackControlador extends Controller
{
    private function requireAdmin(): void
    {
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            header('Location: ' . url('/login') . '?error=' . urlencode('Acceso restringido'));
            exit;
        }
    }

    public function formContacto()
    {
        $this->renderFrontend('frontend/contacto');
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/contacto'));
            exit;
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $asunto = trim($_POST['asunto'] ?? '');
        $mensaje = trim($_POST['mensaje'] ?? '');

        if ($nombre === '' || $email === '' || $asunto === '' || $mensaje === '') {
            header('Location: ' . url('/contacto') . '?error=' . urlencode('Todos los campos son obligatorios'));
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: ' . url('/contacto') . '?error=' . urlencode('Email no válido'));
            exit;
        }

        if (Feedback::crear($nombre, $email, $asunto, $mensaje)) {
            header('Location: ' . url('/contacto') . '?success=' . urlencode('Mensaje enviado correctamente'));
        } else {
            header('Location: ' . url('/contacto') . '?error=' . urlencode('No se pudo enviar el mensaje'));
        }
        exit;
    }

    public function verAdmin()
    {
        $this->requireAdmin();
        $this->renderAdmin('admin/feedback/ver', []);
    }

    public function eliminar($id)
    {
        $this->requireAdmin();
        $id = (int) $id;
        if ($id <= 0 || !Feedback::eliminar($id)) {
            header('Location: ' . url('/admin/feedback') . '?error=' . urlencode('No se pudo eliminar'));
        } else {
            header('Location: ' . url('/admin/feedback') . '?deleted=1');
        }
        exit;
    }
}
