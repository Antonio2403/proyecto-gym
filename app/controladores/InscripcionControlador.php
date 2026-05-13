<?php

require_once 'app/modelos/inscripcion.php';
require_once 'app/modelos/actividades.php';
require_once 'app/modelos/cliente_subscripcion.php';
require_once 'core/Controller.php';

class InscripcionControlador extends Controller
{
    public function misIncripciones()
    {
        $this->redirigirFisioFueraPortal();
        $uid = (int) ($_SESSION['usuario_id'] ?? 0);
        $tienePlan = $uid > 0 && ClienteSubscripcion::tieneSuscripcionActivaPorUsuarioId($uid);
        $inscripciones = Inscripcion::obtenerInscripciones();
        $this->renderFrontend('frontend/verMisActividades', [
            'inscripciones' => $inscripciones,
            'tiene_plan_activo' => $tienePlan,
        ]);
    }

    public function cancelar()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/usuario/inscripciones/mis-inscripciones'));
            exit;
        }

        $this->redirigirFisioFueraPortal();

        $uid = (int) ($_SESSION['usuario_id'] ?? 0);
        if ($uid <= 0) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Debes iniciar sesión'));
            exit;
        }
        if (!ClienteSubscripcion::tieneSuscripcionActivaPorUsuarioId($uid)) {
            header(
                'Location: ' . url('/usuario/inscripciones/mis-inscripciones') . '?error=' . rawurlencode(
                    'Necesitas un plan activo para gestionar reservas. Contrata o renueva en Planes.'
                )
            );
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
        $fecha_sesion = trim((string) ($_POST['fecha_sesion'] ?? ''));
        if ($actividad_id <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_sesion)) {
            header('Location: ' . url('/usuario/actividades') . '?error=' . rawurlencode('Datos de reserva no válidos'));
            exit;
        }

        $usuario_id = (int) $_SESSION['usuario_id'];

        if (!ClienteSubscripcion::tieneSuscripcionActivaPorUsuarioId($usuario_id)) {
            header(
                'Location: ' . url('/usuario/actividades') . '?error=' . rawurlencode(
                    'Necesitas un plan activo y pago al día para reservar. Contrata o renueva en la sección de planes.'
                )
            );
            exit;
        }

        $conexion = BasedeDatos::Conectar();

        // Obtener cliente
        $stmt = $conexion->prepare("SELECT clientes.id, usuarios.email FROM clientes join usuarios ON clientes.usuario_id = usuarios.id WHERE usuarios.id = ?");
        $stmt->execute([$usuario_id]);
        $cliente = $stmt->fetch();

        if (!$cliente) {
            die("No eres cliente");
        }

        $cliente_id = (int) $cliente['id'];
        $cliente_email = $cliente['email'];

        if (!Actividad::fechaEsSesionValida($actividad_id, $fecha_sesion)) {
            header('Location: ' . url('/usuario/actividades') . '?error=' . rawurlencode('La fecha no corresponde a esta actividad'));
            exit;
        }

        if (!ClienteSubscripcion::puedeNuevaReservaEsaSemana($cliente_id, $fecha_sesion)) {
            $cupo = ClienteSubscripcion::cupoReservasSemana($cliente_id, $fecha_sesion);
            $msg = sprintf(
                'Has superado el máximo de inscripciones posibles esta semana con tu plan (%d de %d reservas).',
                $cupo['usado'],
                $cupo['max_semana']
            );
            header('Location: ' . url('/usuario/actividades') . '?error=' . rawurlencode($msg));
            exit;
        }

        if (Inscripcion::yaInscritoEnSesion($cliente_id, $actividad_id, $fecha_sesion)) {
            header('Location: ' . url('/usuario/actividades') . '?info=' . rawurlencode('Ya estás inscrito en esta sesión'));
            exit;
        }

        $actividad = Actividad::obtenerPorId($actividad_id);
        if (!$actividad) {
            header('Location: ' . url('/usuario/actividades') . '?error=' . rawurlencode('Actividad no encontrada'));
            exit;
        }

        $inscritos = Inscripcion::contarInscritosSesion($actividad_id, $fecha_sesion);
        $plazas = max(1, (int) ($actividad['plazas'] ?? 20));
        if ($inscritos >= $plazas) {
            header('Location: ' . url('/usuario/actividades') . '?error=' . rawurlencode('Esta sesión ya está llena'));
            exit;
        }

        Inscripcion::inscribir($cliente_id, $actividad_id, $fecha_sesion);

        $fechaSesionComentarios = $fecha_sesion;

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
