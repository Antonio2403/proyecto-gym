<?php

require_once 'app/modelos/inscripcion.php';
require_once 'app/modelos/actividades.php';
require_once 'core/Controller.php';

class InscripcionControlador extends Controller
{
    public function misIncripciones()
    {
        $this->redirigirFisioFueraPortal();
        $inscripciones = Inscripcion::obtenerInscripciones();
        $this->renderFrontend("frontend/verMisActividades", ['inscripciones' => $inscripciones]);
    }

    public function cancelar()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/usuario/inscripciones/mis-inscripciones'));
            exit;
        }

        $this->redirigirFisioFueraPortal();

        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Debes iniciar sesión'));
            exit;
        }

        $id = (int) ($_POST['inscripcion_id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . url('/usuario/inscripciones/mis-inscripciones') . '?error=1');
            exit;
        }

        if (Inscripcion::cancelarParaUsuario($id, (int) $_SESSION['usuario_id'])) {
            header('Location: ' . url('/usuario/inscripciones/mis-inscripciones') . '?success=1');
        } else {
            header('Location: ' . url('/usuario/inscripciones/mis-inscripciones') . '?error=1');
        }
        exit;
    }

    public function inscribirse()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/usuario/actividades'));
            exit;
        }

        $this->redirigirFisioFueraPortal();

        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Debes iniciar sesión'));
            exit;
        }

        $actividad_id = (int) ($_POST['actividad_id'] ?? 0);
        if ($actividad_id <= 0) {
            header('Location: ' . url('/usuario/actividades') . '?error=' . rawurlencode('Actividad no válida'));
            exit;
        }

        $usuario_id = (int) $_SESSION['usuario_id'];

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
            header('Location: ' . url('/usuario/actividades') . '?error=' . rawurlencode('Ya estás inscrito en esta actividad'));
            exit;
        }

        // Obtener capacidad
        $actividad = Actividad::obtenerPorId($actividad_id);
        $inscritos = Inscripcion::contarInscritos($actividad_id);

        // Clase llena
        if ($inscritos >= $actividad['plazas']) {
            header('Location: ' . url('/usuario/actividades') . '?error=' . rawurlencode('Esta actividad ya está llena'));
            exit;
        }

        // Insertar
        Inscripcion::inscribir($cliente_id, $actividad_id);

        $fechaSesionComentarios = Inscripcion::fechaProximaOcurrenciaActividad($actividad_id);

        require_once dirname(__DIR__, 2) . '/core/helpers/mail_smtp.php';
        $mailErrBooking = null;
        if (!gp_mail_send(
            $cliente_email,
            'Confirmación de reserva',
            '<p>Has reservado tu plaza correctamente.</p>',
            'Has reservado tu plaza correctamente.',
            $mailErrBooking
        )) {
            error_log('[Spartum reserva] correo no enviado: ' . (string) $mailErrBooking);
        }

        if ($fechaSesionComentarios !== null && $fechaSesionComentarios !== '') {
            $params = [
                'actividad_id' => $actividad_id,
                'fecha' => $fechaSesionComentarios,
                'orden' => 'desc',
                'info' => 'Reserva realizada. Aquí aparecerán los comentarios de esta sesión; podrás dejar la tuya cuando termine.',
            ];
            header('Location: ' . url('/usuario/actividades/sesion/comentarios') . '?' . http_build_query($params));
        } else {
            header('Location: ' . url('/usuario/actividades') . '?success=' . rawurlencode('Has reservado tu plaza con exito'));
        }
        exit;
    }
}
