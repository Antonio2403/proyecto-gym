<?php

require_once 'core/Controller.php';
require_once 'app/modelos/usuario.php';
require_once 'app/modelos/cliente.php';
require_once 'app/modelos/recuperacion_cuenta_ticket.php';

class ReactivarCuentaControlador extends Controller
{
    private function normalizarCodigoUsuario(string $raw): string
    {
        $t = preg_replace('/[^0-9]/', '', $raw) ?? '';

        return strlen($t) === 6 ? substr($t, 0, 3) . '-' . substr($t, 3, 3) : trim($raw);
    }

    private function mascararTelefonoEspana(?string $telefonoRaw): string
    {
        $tel9 = fv_telefono_es_a_digitos9((string) $telefonoRaw);
        if ($tel9 === null || strlen($tel9) !== 9) {
            return '···········';
        }

        return substr($tel9, 0, 3) . ' ··· ·· ' . substr($tel9, -2);
    }

    public function formulario(): void
    {
        header('Location: ' . url('/ticket') . '?tipo=reactivacion');
        exit;
    }

    public function solicitarCodigo(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/ticket') . '?tipo=reactivacion');
            exit;
        }

        $dni = trim((string) ($_POST['dni'] ?? ''));
        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        $row = Usuario::obtenerIdEmailPorDniYTelefono($dni, $telefono);
        if ($row === null) {
            header('Location: ' . url('/ticket') . '?tipo=reactivacion&error=' . rawurlencode('No hemos podido verificar el DNI y teléfono de la cuenta.'));
            exit;
        }

        $usuario = Usuario::obtenerPorId((int) $row['id']);
        if (!$usuario) {
            header('Location: ' . url('/ticket') . '?tipo=reactivacion&error=' . rawurlencode('No se pudo cargar la cuenta.'));
            exit;
        }

        $bloqueo = Usuario::estadoBloqueo((int) $usuario->getId());
        if (($bloqueo['tipo'] ?? 'N') !== 'T') {
            header('Location: ' . url('/ticket') . '?tipo=reactivacion&error=' . rawurlencode('Esta cuenta no está en baja normal o no admite reactivación por ticket.'));
            exit;
        }

        $ticket = RecuperacionCuentaTicket::intentarCrear((int) $usuario->getId(), 'reactivacion');
        if (empty($ticket['ok'])) {
            header('Location: ' . url('/ticket') . '?tipo=reactivacion&error=' . rawurlencode((string) ($ticket['error'] ?? 'No se pudo crear el ticket.')));
            exit;
        }

        $_SESSION['gp_ticket_cancel_auth'] = [
            'ticket_id' => (int) $ticket['id'],
            'usuario_id' => (int) $usuario->getId(),
        ];

        header('Location: ' . url('/ticket') . '?paso=solicitud&solicitud=' . (int) $ticket['id']);
        exit;
    }

    public function verificarCodigo(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/ticket') . '?tipo=reactivacion');
            exit;
        }

        $sid = (int) ($_POST['solicitud_id'] ?? 0);
        $ticket = $sid > 0 ? RecuperacionCuentaTicket::obtenerPendientePorId($sid) : null;
        if (!$ticket) {
            header('Location: ' . url('/ticket') . '?tipo=reactivacion&error=' . rawurlencode('Código caducado o solicitud no válida.'));
            exit;
        }
        if (($ticket['tipo'] ?? 'recuperacion') !== 'reactivacion') {
            header('Location: ' . url('/ticket') . '?tipo=reactivacion&error=' . rawurlencode('Este ticket no es de reactivación.'));
            exit;
        }

        $intro = $this->normalizarCodigoUsuario((string) ($_POST['codigo_verificacion'] ?? ''));
        if ($intro !== (string) $ticket['codigo']) {
            $fails = RecuperacionCuentaTicket::registrarIntentoCodigoFallido($sid, 5);
            $msg = $fails >= 5 ? 'Demasiados intentos. Solicita un nuevo código.' : 'Código incorrecto.';
            header('Location: ' . url('/ticket') . '?paso=codigo&tipo=reactivacion&error=' . rawurlencode($msg));
            exit;
        }

        $uid = (int) $ticket['usuario_id'];
        $bloqueo = Usuario::estadoBloqueo($uid);
        if (($bloqueo['tipo'] ?? 'N') !== 'T') {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Esta cuenta no se puede reactivar con este ticket.'));
            exit;
        }

        Usuario::desbloquear($uid);
        RecuperacionCuentaTicket::marcarUsado($sid);
        unset($_SESSION['gp_ticket_cancel_auth']);
        header('Location: ' . url('/login') . '?success=' . rawurlencode('Cuenta reactivada. Ya puedes iniciar sesión; recuerda contratar un plan si quieres reservar.'));
        exit;
    }
}
