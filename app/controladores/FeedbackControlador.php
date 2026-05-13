<?php

require_once 'core/Controller.php';
require_once 'app/modelos/feedback.php';
require_once dirname(__DIR__, 2) . '/core/helpers/mail_smtp.php';

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

    public function formResponder($id): void
    {
        $this->requireAdmin();
        $fid = (int) $id;
        $row = Feedback::obtenerPorId($fid);
        if (!$row) {
            header('Location: ' . url('/admin/feedback') . '?error=' . rawurlencode('Mensaje no encontrado'));
            exit;
        }
        $this->renderAdmin('admin/feedback/responder', ['fb' => $row]);
    }

    public function enviarRespuesta(): void
    {
        $this->requireAdmin();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/admin/feedback'));
            exit;
        }
        if (!csrf_validate_request()) {
            header('Location: ' . url('/admin/feedback') . '?error=' . rawurlencode('Sesión caducada o token no válido. Vuelve a intentarlo.'));
            exit;
        }
        $fid = (int) ($_POST['feedback_id'] ?? 0);
        $texto = trim((string) ($_POST['respuesta'] ?? ''));
        if ($fid <= 0 || $texto === '') {
            header('Location: ' . url('/admin/feedback') . '?error=' . rawurlencode('Datos no válidos'));
            exit;
        }
        if (mb_strlen($texto) > 8000) {
            header('Location: ' . url('/admin/feedback/responder/' . $fid) . '?error=' . rawurlencode('El mensaje es demasiado largo (máx. 8000 caracteres).'));
            exit;
        }
        $row = Feedback::obtenerPorId($fid);
        if (!$row) {
            header('Location: ' . url('/admin/feedback') . '?error=' . rawurlencode('Mensaje no encontrado'));
            exit;
        }
        $to = strtolower(trim((string) ($row['email'] ?? '')));
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            header('Location: ' . url('/admin/feedback/responder/' . $fid) . '?error=' . rawurlencode('El email del remitente no es válido.'));
            exit;
        }
        $nombre = htmlspecialchars(trim((string) ($row['nombre'] ?? '')), ENT_QUOTES, 'UTF-8');
        $asuntoOrig = trim((string) ($row['asunto'] ?? ''));
        $subj = 'Re: ' . ($asuntoOrig !== '' ? $asuntoOrig : 'Tu mensaje a Spartum');
        $safeBody = nl2br(htmlspecialchars($texto, ENT_QUOTES, 'UTF-8'));
        $origBlock = nl2br(htmlspecialchars((string) ($row['mensaje'] ?? ''), ENT_QUOTES, 'UTF-8'));
        $html = '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><title>Respuesta</title></head><body style="font-family:system-ui,Segoe UI,sans-serif;line-height:1.5;color:#222;max-width:560px;margin:0 auto;padding:24px;">'
            . '<p>Hola ' . $nombre . ',</p>'
            . '<p>Te respondemos desde <strong>Spartum</strong> respecto a tu mensaje de contacto:</p>'
            . '<div style="border-left:4px solid #e85d04;padding:12px 16px;margin:16px 0;background:#faf7f5;">' . $safeBody . '</div>'
            . '<p style="font-size:13px;color:#555;"><strong>Tu mensaje original:</strong></p>'
            . '<div style="font-size:13px;color:#666;border:1px solid #eee;padding:12px;border-radius:8px;">' . $origBlock . '</div>'
            . '<p style="margin-top:24px;font-size:13px;color:#666;">Un saludo,<br><strong>Spartum</strong></p>'
            . '</body></html>';
        $plain = "Hola,\n\nRespuesta de Spartum:\n\n" . $texto . "\n\n--- Tu mensaje original ---\n" . (string) ($row['mensaje'] ?? '') . "\n\n— Spartum\n";
        $err = null;
        if (!gp_mail_send($to, $subj, $html, $plain, $err)) {
            header(
                'Location: ' . url('/admin/feedback/responder/' . $fid) . '?error=' . rawurlencode(
                    $err !== null && $err !== '' ? $err : 'No se pudo enviar el correo.'
                )
            );
            exit;
        }
        header('Location: ' . url('/admin/feedback') . '?success=' . rawurlencode('Respuesta enviada a ' . $to));
        exit;
    }
}
