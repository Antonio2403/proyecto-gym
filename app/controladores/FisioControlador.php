<?php

require_once 'core/Controller.php';
require_once 'app/modelos/cliente.php';
require_once 'app/modelos/cliente_subscripcion.php';
require_once 'app/modelos/fisioterapeuta.php';
require_once 'app/modelos/cita.php';
require_once dirname(__DIR__, 2) . '/core/helpers/horario_centro.php';

class FisioControlador extends Controller
{
    private function exigirCliente(): int
    {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Debes iniciar sesión'));
            exit;
        }
        if (($_SESSION['rol'] ?? '') !== 'cliente') {
            header('Location: ' . url('/inicio') . '?error=' . rawurlencode('Solo clientes pueden acceder a fisioterapia'));
            exit;
        }
        $clienteId = Cliente::IdClientePorUsuarioId((int) $_SESSION['usuario_id']);
        if (!$clienteId) {
            header('Location: ' . url('/inicioUsuario') . '?error=' . rawurlencode('No se encontró tu perfil de cliente'));
            exit;
        }

        return (int) $clienteId;
    }

    /**
     * Redirige si no hay suscripción activa con fisioterapia incluida.
     */
    private function exigirSuscripcionFisio(int $clienteId): array
    {
        $sub = ClienteSubscripcion::obtenerActivaConFisio($clienteId);
        if (!$sub) {
            header(
                'Location: ' . url('/usuario/fisio') . '?error=' . rawurlencode(
                    'Necesitas un plan activo que incluya fisioterapia.'
                )
            );
            exit;
        }

        return $sub;
    }

    public function index()
    {
        $clienteId = $this->exigirCliente();
        $planFisio = ClienteSubscripcion::obtenerActivaConFisio($clienteId);

        $this->renderFrontend('frontend/fisio/index', [
            'tiene_fisio' => $planFisio !== null,
            'plan' => $planFisio,
        ]);
    }

    public function formSolicitar()
    {
        $clienteId = $this->exigirCliente();
        $this->exigirSuscripcionFisio($clienteId);

        $fisioterapeutas = Fisioterapeuta::obtenerTodas();
        $this->renderFrontend('frontend/fisio/solicitar', [
            'fisioterapeutas' => $fisioterapeutas,
            'horas_disponibles_url' => url('/usuario/fisio/horas-disponibles'),
            'min_fecha_cita' => date('Y-m-d'),
        ]);
    }

    public function horasDisponibles()
    {
        $clienteId = $this->exigirCliente();
        $this->exigirSuscripcionFisio($clienteId);

        header('Content-Type: application/json; charset=utf-8');

        $fisioId = isset($_GET['fisio_id']) ? (int) $_GET['fisio_id'] : 0;
        $fecha = trim((string) ($_GET['fecha'] ?? ''));

        if ($fisioId <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            echo json_encode(['ok' => false, 'horas' => [], 'mensaje' => 'Datos no válidos']);
            exit;
        }

        if (!Fisioterapeuta::obtenerPorId($fisioId)) {
            echo json_encode(['ok' => false, 'horas' => [], 'mensaje' => 'Fisioterapeuta no válido']);
            exit;
        }

        $horas = Cita::horasDisponiblesFisioDia($fisioId, $fecha);
        $diaCerrado = gp_horario_slots_centro_dia($fecha) === [];

        echo json_encode([
            'ok' => true,
            'horas' => $horas,
            'dia_completo' => !$diaCerrado && $horas === [],
            'dia_cerrado' => $diaCerrado,
        ]);
        exit;
    }

    public function solicitar()
    {
        $clienteId = $this->exigirCliente();
        $this->exigirSuscripcionFisio($clienteId);

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/usuario/fisio/solicitar'));
            exit;
        }

        $fisioId = isset($_POST['fisio_id']) ? (int) $_POST['fisio_id'] : 0;
        $fechaCita = trim((string) ($_POST['fecha_cita'] ?? ''));
        $horaCita = trim((string) ($_POST['hora_cita'] ?? ''));
        $motivo = trim((string) ($_POST['motivo'] ?? ''));

        if ($fisioId <= 0 || $fechaCita === '' || $horaCita === '' || $motivo === '') {
            header(
                'Location: ' . url('/usuario/fisio/solicitar') . '?error=' . rawurlencode('Completa todos los campos.')
            );
            exit;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaCita) || !preg_match('/^\d{2}:\d{2}$/', $horaCita)) {
            header(
                'Location: ' . url('/usuario/fisio/solicitar') . '?error=' . rawurlencode('Fecha u hora no válidas.')
            );
            exit;
        }

        if (mb_strlen($motivo) > 2000) {
            header(
                'Location: ' . url('/usuario/fisio/solicitar') . '?error=' . rawurlencode('El motivo es demasiado largo (máx. 2000 caracteres).')
            );
            exit;
        }

        $fisio = Fisioterapeuta::obtenerPorId($fisioId);
        if (!$fisio) {
            header(
                'Location: ' . url('/usuario/fisio/solicitar') . '?error=' . rawurlencode('Fisioterapeuta no válido.')
            );
            exit;
        }

        if (!gp_horario_es_slot_valido_fisio($fechaCita, $horaCita)) {
            header(
                'Location: ' . url('/usuario/fisio/solicitar') . '?error=' . rawurlencode('La hora elegida no es una franja válida del centro.')
            );
            exit;
        }

        $fechaLocal = $fechaCita . 'T' . $horaCita;
        $ts = strtotime($fechaCita . ' ' . $horaCita . ':00');
        if ($ts === false) {
            header(
                'Location: ' . url('/usuario/fisio/solicitar') . '?error=' . rawurlencode('Fecha u hora no válidas.')
            );
            exit;
        }

        if ($ts < time()) {
            header(
                'Location: ' . url('/usuario/fisio/solicitar') . '?error=' . rawurlencode('Elige una fecha y hora futuras.')
            );
            exit;
        }

        $fechaDt = (new DateTimeImmutable())->setTimestamp($ts);
        $horarioErr = gp_horario_validar_cita_fisio($fechaDt);
        if ($horarioErr !== null) {
            header(
                'Location: ' . url('/usuario/fisio/solicitar') . '?error=' . rawurlencode($horarioErr)
            );
            exit;
        }

        $fechaMysql = $fechaDt->format('Y-m-d H:i:s');

        $disponibles = Cita::horasDisponiblesFisioDia($fisioId, $fechaCita);
        if (!in_array($horaCita, $disponibles, true)) {
            header(
                'Location: ' . url('/usuario/fisio/solicitar') . '?error=' . rawurlencode(
                    'Ese hueco ya no está disponible. Elige otra hora o día.'
                )
            );
            exit;
        }

        // Blindaje: volver a validar suscripción con fisio
        if (!ClienteSubscripcion::tieneFisioActivo($clienteId)) {
            header(
                'Location: ' . url('/usuario/fisio') . '?error=' . rawurlencode('Tu suscripción con fisioterapia ya no está activa.')
            );
            exit;
        }

        if (Cita::crear($clienteId, $fisioId, $fechaMysql, $motivo)) {
            header('Location: ' . url('/usuario/fisio/mis-citas') . '?success=1');
            exit;
        }

        header(
            'Location: ' . url('/usuario/fisio/solicitar') . '?error=' . rawurlencode('No se pudo registrar la cita. El hueco puede estar ocupado; prueba otra hora.')
        );
        exit;
    }

    public function misCitas()
    {
        $clienteId = $this->exigirCliente();
        $tieneFisio = ClienteSubscripcion::tieneFisioActivo($clienteId);
        $citas = Cita::listarPorCliente($clienteId);
        $this->renderFrontend('frontend/fisio/misCitas', [
            'citas' => $citas,
            'tiene_fisio' => $tieneFisio,
        ]);
    }

    public function cancelarCita()
    {
        $clienteId = $this->exigirCliente();

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/usuario/fisio/mis-citas'));
            exit;
        }

        $citaId = isset($_POST['cita_id']) ? (int) $_POST['cita_id'] : 0;
        if ($citaId <= 0 || !Cita::perteneceACliente($citaId, $clienteId)) {
            header('Location: ' . url('/usuario/fisio/mis-citas') . '?error=' . rawurlencode('Cita no válida.'));
            exit;
        }

        if (Cita::cancelarSiSolicitada($citaId, $clienteId)) {
            header('Location: ' . url('/usuario/fisio/mis-citas') . '?cancelada=1');
            exit;
        }

        header(
            'Location: ' . url('/usuario/fisio/mis-citas') . '?error=' . rawurlencode('No se pudo cancelar (solo citas pendientes).')
        );
        exit;
    }
}
