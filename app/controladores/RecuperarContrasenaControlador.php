<?php

require_once 'core/Controller.php';
require_once 'app/modelos/usuario.php';
require_once 'app/modelos/recuperacion_cuenta_ticket.php';
require_once dirname(__DIR__, 2) . '/core/helpers/mail_smtp.php';

class RecuperarContrasenaControlador extends Controller
{
    private function limpiarFlujoDni(): void
    {
        foreach ([
            'recovery_ok_user_id',
            'recovery_ok_email',
            'recovery_ok_tipo',
            'recovery_verify_fails',
        ] as $k) {
            unset($_SESSION[$k]);
        }
    }

    private function limpiarSesionTicketCancelAuth(): void
    {
        unset($_SESSION['gp_ticket_cancel_auth']);
    }

    /** Solo la misma sesión que acaba de crear el ticket puede cancelarlo (evita cancelar con solo el enlace). */
    private function recordarSesionTicketRecienCreado(int $ticketId, int $usuarioId): void
    {
        $_SESSION['gp_ticket_cancel_auth'] = [
            'ticket_id' => $ticketId,
            'usuario_id' => $usuarioId,
        ];
    }

    private function sesionPuedeCancelarTicket(int $ticketId, int $usuarioId): bool
    {
        $auth = $_SESSION['gp_ticket_cancel_auth'] ?? null;
        if (!is_array($auth)) {
            return false;
        }

        return (int) ($auth['ticket_id'] ?? 0) === $ticketId
            && (int) ($auth['usuario_id'] ?? 0) === $usuarioId;
    }

    /**
     * Tras validar el código de recepción contra un ticket pendiente.
     *
     * @param array<string,mixed> $t fila de recuperacion_cuenta_ticket
     */
    private function finalizarFlujoTicketVerificado(array $t, int $sid): void
    {
        $tipoTicket = $this->tipoTicketActivo($t);
        $uid = (int) $t['usuario_id'];
        $u = Usuario::obtenerPorId($uid);
        if (!$u) {
            header('Location: ' . url('/ticket') . '?error=' . rawurlencode('Error al recuperar la cuenta.'));
            exit;
        }

        if ($tipoTicket === 'reactivacion') {
            $bloqueo = Usuario::estadoBloqueo($uid);
            if (($bloqueo['tipo'] ?? 'N') !== 'T') {
                header('Location: ' . url('/ticket') . '?tipo=reactivacion&error=' . rawurlencode('Esta cuenta no se puede reactivar con este ticket.'));
                exit;
            }

            Usuario::desbloquear($uid);
            RecuperacionCuentaTicket::marcarUsado($sid);
            $this->limpiarSesionTicketCancelAuth();
            header('Location: ' . url('/login') . '?success=' . rawurlencode('Cuenta reactivada. Ya puedes iniciar sesión; recuerda contratar un plan si quieres reservar.'));
            exit;
        }

        RecuperacionCuentaTicket::marcarUsado($sid);
        $this->limpiarSesionTicketCancelAuth();

        $_SESSION['recovery_ok_user_id'] = $uid;
        $_SESSION['recovery_ok_email'] = strtolower(trim($u->getEmail()));
        $_SESSION['recovery_ok_tipo'] = $tipoTicket;

        header('Location: ' . url('/ticket') . '?paso=correo');
        exit;
    }

    private function normalizarTipoTicket(string $tipo): string
    {
        $tipo = strtolower(trim($tipo));
        if (in_array($tipo, ['correo', 'contrasena', 'reactivacion'], true)) {
            return $tipo;
        }

        return 'correo';
    }

    private function tipoTicketActivo(array $ticket): string
    {
        return $this->normalizarTipoTicket((string) ($ticket['tipo'] ?? 'correo'));
    }

    private function normalizarCodigoUsuario(string $raw): string
    {
        $t = preg_replace('/[^0-9]/', '', $raw) ?? '';

        return strlen($t) === 6 ? substr($t, 0, 3) . '-' . substr($t, 3, 3) : strtoupper(trim($raw));
    }

    private function mascararTelefonoEspana(?string $telefonoRaw): string
    {
        $tel9 = fv_telefono_es_a_digitos9((string) $telefonoRaw);
        if ($tel9 === null || strlen($tel9) !== 9) {
            return '···········';
        }

        return substr($tel9, 0, 3) . ' ··· ·· ' . substr($tel9, -2);
    }

    private function throttleDemasiadasPeticiones(): bool
    {
        $now = time();
        $start = (int) ($_SESSION['recovery_throttle_start'] ?? 0);
        if ($start < $now - 900) {
            $_SESSION['recovery_throttle_start'] = $now;
            $_SESSION['recovery_throttle_count'] = 0;
        }
        $_SESSION['recovery_throttle_count'] = (int) ($_SESSION['recovery_throttle_count'] ?? 0) + 1;

        return $_SESSION['recovery_throttle_count'] > 20;
    }

    private function enviarCorreoReset(int $usuarioId, string $email): bool
    {
        $token = bin2hex(random_bytes(32));
        $expira = (new DateTimeImmutable('+2 hours'))->format('Y-m-d H:i:s');
        if (!Usuario::guardarTokenRecuperacion($usuarioId, $token, $expira)) {
            return false;
        }

        $usuario = Usuario::obtenerPorId($usuarioId);
        $nombre = $usuario ? trim($usuario->getNombre() . ' ' . $usuario->getApellido1()) : '';
        $resetUrl = app_url_absolute('/restablecer-contrasena?' . http_build_query(['token' => $token]));
        $safeName = htmlspecialchars($nombre !== '' ? $nombre : 'usuario', ENT_QUOTES, 'UTF-8');
        $asunto = 'Restablecer contraseña — Spartum';
        $html = '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"></head><body style="font-family:system-ui,sans-serif;line-height:1.5;color:#222;max-width:560px;margin:0 auto;padding:24px;">'
            . '<p>Hola ' . $safeName . ',</p>'
            . '<p>Has solicitado restablecer tu contraseña en <strong>Spartum</strong>. El enlace caduca en <strong>2 horas</strong>.</p>'
            . '<p style="margin:28px 0;"><a href="' . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;background:#e85d04;color:#fff;text-decoration:none;padding:12px 22px;border-radius:8px;font-weight:600;">Elegir nueva contraseña</a></p>'
            . '<p style="font-size:13px;color:#555;">Si no pediste este cambio, ignora el mensaje.</p>'
            . '</body></html>';
        $texto = "Hola {$nombre},\n\nRestablece tu contraseña (válido 2 horas):\n{$resetUrl}\n\nSi no fuiste tú, ignora este correo.\n";

        $mailErr = null;

        return gp_mail_send($email, $asunto, $html, $texto, $mailErr);
    }

    private function cargarTicketPendienteVista(int $solicitudId): ?array
    {
        if ($solicitudId <= 0) {
            return null;
        }
        $t = RecuperacionCuentaTicket::obtenerPendientePorId($solicitudId);
        if ($t === null) {
            return null;
        }
        $u = Usuario::obtenerPorId((int) $t['usuario_id']);
        if (!$u) {
            return null;
        }

        return [
            'ticket' => $t,
            'tipo_ticket' => $this->tipoTicketActivo($t),
            'telefono_mascara' => $this->mascararTelefonoEspana($u->getTelefono()),
        ];
    }

    public function formulario()
    {
        $paso = trim((string) ($_GET['paso'] ?? ''));
        if ($paso === '') {
            $this->limpiarFlujoDni();
        }

        $data = [
            'paso' => $paso,
            'telefono_mascara' => null,
            'email_revelado' => null,
            'solicitud_id' => null,
            'tipo_ticket' => $this->normalizarTipoTicket((string) ($_GET['tipo'] ?? 'correo')),
            'ticket_expira_en' => null,
            'codigo_necesita_dni' => false,
            'ticket_codigo_desde_sesion' => false,
        ];

        if ($paso === 'solicitud') {
            $sid = (int) ($_GET['solicitud'] ?? 0);
            $pack = $this->cargarTicketPendienteVista($sid);
            if ($pack === null) {
                header('Location: ' . url('/ticket') . '?error=' . rawurlencode('Solicitud no encontrada o ya no está pendiente.'));
                exit;
            }
            $data['solicitud_id'] = $sid;
            $data['tipo_ticket'] = $pack['tipo_ticket'];
            $data['telefono_mascara'] = $pack['telefono_mascara'];
            $expRaw = (string) ($pack['ticket']['expira_en'] ?? '');
            if ($expRaw !== '') {
                try {
                    $data['ticket_expira_en'] = (new DateTimeImmutable($expRaw))->format('d/m/Y H:i');
                } catch (\Throwable $e) {
                    $data['ticket_expira_en'] = $expRaw;
                }
            }
        }

        if ($paso === 'codigo') {
            $forzarIdentidad = (string) ($_GET['forzar_identidad'] ?? '') === '1';
            if ($forzarIdentidad) {
                $data['codigo_necesita_dni'] = true;
            } else {
                $sidGet = (int) ($_GET['solicitud'] ?? 0);
                $sid = 0;
                $desdeSesion = false;

                if ($sidGet > 0) {
                    if ($this->cargarTicketPendienteVista($sidGet) === null) {
                        header(
                            'Location: ' . url('/ticket') . '?paso=codigo&error=' . rawurlencode(
                                'El número de ticket de la dirección no es válido o ya caducó. Si abriste el ticket en este navegador, vuelve a «Ya tengo el código» desde la página principal del ticket (no hace falta recordar el número).'
                            )
                        );
                        exit;
                    }
                    $sid = $sidGet;
                } else {
                    $auth = $_SESSION['gp_ticket_cancel_auth'] ?? null;
                    if (is_array($auth)) {
                        $try = (int) ($auth['ticket_id'] ?? 0);
                        if ($try > 0 && $this->cargarTicketPendienteVista($try) !== null) {
                            $sid = $try;
                            $desdeSesion = true;
                        }
                    }
                }

                if ($sid > 0) {
                    $pack = $this->cargarTicketPendienteVista($sid);
                    if ($pack === null) {
                        header('Location: ' . url('/ticket') . '?paso=codigo&error=' . rawurlencode('Solicitud no válida o caducada.'));
                        exit;
                    }
                    $data['solicitud_id'] = $sid;
                    $data['tipo_ticket'] = $pack['tipo_ticket'];
                    $data['telefono_mascara'] = $pack['telefono_mascara'];
                    $data['ticket_codigo_desde_sesion'] = $desdeSesion;
                } else {
                    $data['codigo_necesita_dni'] = true;
                }
            }
        }

        if ($paso === 'correo') {
            if (empty($_SESSION['recovery_ok_email']) || empty($_SESSION['recovery_ok_user_id'])) {
                header('Location: ' . url('/ticket') . '?error=' . rawurlencode('Completa primero la verificación.'));
                exit;
            }
            $data['email_revelado'] = (string) $_SESSION['recovery_ok_email'];
            $data['tipo_ticket'] = $this->normalizarTipoTicket((string) ($_SESSION['recovery_ok_tipo'] ?? 'correo'));
        }

        $this->renderFrontend('frontend/recuperarContrasena', $data);
    }

    public function continuarPorTicketToken(string $tokenRaw)
    {
        $row = RecuperacionCuentaTicket::obtenerPendientePorToken($tokenRaw);
        if ($row === null) {
            header('Location: ' . url('/ticket') . '?error=' . rawurlencode('El enlace no es válido o ya se ha usado. Solicita uno nuevo en recepción.'));
            exit;
        }
        if ($this->tipoTicketActivo($row) !== 'contrasena') {
            header('Location: ' . url('/ticket') . '?error=' . rawurlencode('Este enlace no corresponde a cambio de contraseña.'));
            exit;
        }

        $uid = (int) $row['usuario_id'];
        $u = Usuario::obtenerPorId($uid);
        if (!$u) {
            header('Location: ' . url('/ticket') . '?error=' . rawurlencode('No se pudo completar la recuperación.'));
            exit;
        }

        RecuperacionCuentaTicket::marcarUsado((int) $row['id']);
        $this->limpiarSesionTicketCancelAuth();
        $_SESSION['recovery_ok_user_id'] = $uid;
        $_SESSION['recovery_ok_email'] = strtolower(trim($u->getEmail()));
        $_SESSION['recovery_ok_tipo'] = 'contrasena';

        header('Location: ' . url('/ticket') . '?paso=correo');
        exit;
    }

    public function solicitarPorEmail()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/ticket'));
            exit;
        }

        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        if ($email === '' || !fv_email_valido($email)) {
            header('Location: ' . url('/ticket') . '?tipo=contrasena&error=' . rawurlencode('Indica un email válido'));
            exit;
        }

        $usuario = Usuario::obtenerPorEmail($email);
        $okMsg = 'Si existe una cuenta con ese email, recibirás un enlace para restablecer la contraseña. Revisa también spam.';

        if (!$usuario) {
            header('Location: ' . url('/login') . '?success=' . rawurlencode($okMsg));
            exit;
        }

        if (!$this->enviarCorreoReset((int) $usuario->getId(), $email)) {
            error_log('[Spartum reset] correo no enviado');
        }

        header('Location: ' . url('/login') . '?success=' . rawurlencode($okMsg));
        exit;
    }

    public function dniVerificarTelefono()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/ticket'));
            exit;
        }

        if ($this->throttleDemasiadasPeticiones()) {
            header('Location: ' . url('/ticket') . '?error=' . rawurlencode('Demasiados intentos. Espera unos minutos e inténtalo de nuevo.'));
            exit;
        }

        $dni = trim((string) ($_POST['dni'] ?? ''));
        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        $tipoTicket = $this->normalizarTipoTicket((string) ($_POST['tipo_ticket'] ?? 'correo'));
        if ($tipoTicket === 'reactivacion') {
            header('Location: ' . url('/ticket') . '?tipo=reactivacion&error=' . rawurlencode('Usa el formulario de reactivación para cuentas dadas de baja.'));
            exit;
        }

        $row = Usuario::obtenerIdEmailPorDniYTelefono($dni, $telefono);
        $msgGen = 'No hemos podido verificar los datos. Comprueba el DNI o NIE y el teléfono asociado a tu cuenta.';

        if ($row === null) {
            header('Location: ' . url('/ticket') . '?tipo=' . rawurlencode($tipoTicket) . '&error=' . rawurlencode($msgGen));
            exit;
        }

        $tel9 = fv_telefono_es_a_digitos9($telefono);
        if ($tel9 === null) {
            header('Location: ' . url('/ticket') . '?tipo=' . rawurlencode($tipoTicket) . '&error=' . rawurlencode($msgGen));
            exit;
        }

        $uid = (int) $row['id'];
        $ticket = RecuperacionCuentaTicket::intentarCrear($uid, $tipoTicket);
        if (empty($ticket['ok'])) {
            header('Location: ' . url('/ticket') . '?tipo=' . rawurlencode($tipoTicket) . '&error=' . rawurlencode((string) ($ticket['error'] ?? 'No se pudo crear el ticket.')));
            exit;
        }

        $this->recordarSesionTicketRecienCreado((int) $ticket['id'], $uid);

        header(
            'Location: ' . url('/ticket')
                . '?paso=solicitud&solicitud=' . (int) $ticket['id']
        );
        exit;
    }

    public function cancelarTicketUsuario(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/ticket'));
            exit;
        }

        $sid = (int) ($_POST['solicitud_id'] ?? 0);
        $t = $sid > 0 ? RecuperacionCuentaTicket::obtenerPendientePorId($sid) : null;
        if ($t === null) {
            header('Location: ' . url('/ticket') . '?error=' . rawurlencode('Este ticket no se puede cancelar (no existe, ya caducó o ya no está pendiente).'));
            exit;
        }

        $uid = (int) $t['usuario_id'];
        if (!$this->sesionPuedeCancelarTicket($sid, $uid)) {
            header('Location: ' . url('/ticket') . '?error=' . rawurlencode('No puedes cancelar este ticket desde este dispositivo. Abre el enlace en el mismo navegador donde lo creaste, o espera a que caduque.'));
            exit;
        }

        if (!RecuperacionCuentaTicket::cancelarPorUsuario($sid, $uid)) {
            header('Location: ' . url('/ticket') . '?error=' . rawurlencode('No se pudo cancelar el ticket.'));
            exit;
        }

        $this->limpiarSesionTicketCancelAuth();
        header('Location: ' . url('/ticket') . '?success=' . rawurlencode('Ticket cancelado. Podrás crear otro pasadas 48 horas desde ahora.'));
        exit;
    }

    public function dniVerificarCodigo()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/ticket'));
            exit;
        }

        $sid = (int) ($_POST['solicitud_id'] ?? 0);
        $t = $sid > 0 ? RecuperacionCuentaTicket::obtenerPendientePorId($sid) : null;
        if ($t === null) {
            header('Location: ' . url('/ticket') . '?paso=codigo&error=' . rawurlencode('Solicitud no válida o caducada.'));
            exit;
        }
        $esperado = (string) $t['codigo'];
        $intro = $this->normalizarCodigoUsuario((string) ($_POST['codigo_verificacion'] ?? ''));

        if ($intro !== $esperado) {
            $fails = RecuperacionCuentaTicket::registrarIntentoCodigoFallido($sid, 5);
            if ($fails >= 5) {
                header('Location: ' . url('/ticket') . '?error=' . rawurlencode('Demasiados intentos incorrectos. Debes crear una nueva solicitud en recepción.'));
                exit;
            }
            header(
                'Location: ' . url('/ticket')
                    . '?paso=codigo&solicitud=' . $sid
                    . '&error=' . rawurlencode('El código no coincide. Inténtalo de nuevo.')
            );
            exit;
        }

        $this->finalizarFlujoTicketVerificado($t, $sid);
    }

    public function dniTelefonoVerificarCodigo(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/ticket') . '?paso=codigo');
            exit;
        }

        if ($this->throttleDemasiadasPeticiones()) {
            header('Location: ' . url('/ticket') . '?paso=codigo&error=' . rawurlencode('Demasiados intentos. Espera unos minutos e inténtalo de nuevo.'));
            exit;
        }

        $dni = trim((string) ($_POST['dni'] ?? ''));
        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        $row = Usuario::obtenerIdEmailPorDniYTelefono($dni, $telefono);
        $msgGen = 'No hemos podido verificar los datos o no tienes un ticket pendiente. Comprueba DNI, teléfono y que el ticket siga vigente.';

        if ($row === null) {
            header('Location: ' . url('/ticket') . '?paso=codigo&error=' . rawurlencode($msgGen));
            exit;
        }

        $tel9 = fv_telefono_es_a_digitos9($telefono);
        if ($tel9 === null) {
            header('Location: ' . url('/ticket') . '?paso=codigo&error=' . rawurlencode($msgGen));
            exit;
        }

        $uid = (int) $row['id'];
        $t = RecuperacionCuentaTicket::obtenerPendienteVigentePorUsuario($uid);
        if ($t === null) {
            header('Location: ' . url('/ticket') . '?paso=codigo&error=' . rawurlencode('No hay ningún ticket pendiente para esta cuenta. Crea uno nuevo desde la página del ticket o espera si cancelaste uno hace menos de 48 horas.'));
            exit;
        }

        $sid = (int) $t['id'];
        $esperado = (string) $t['codigo'];
        $intro = $this->normalizarCodigoUsuario((string) ($_POST['codigo_verificacion'] ?? ''));

        if ($intro !== $esperado) {
            $fails = RecuperacionCuentaTicket::registrarIntentoCodigoFallido($sid, 5);
            if ($fails >= 5) {
                header('Location: ' . url('/ticket') . '?error=' . rawurlencode('Demasiados intentos incorrectos. Debes crear una nueva solicitud en recepción.'));
                exit;
            }
            header(
                'Location: ' . url('/ticket')
                    . '?paso=codigo&solicitud=' . $sid
                    . '&error=' . rawurlencode('El código no coincide. Inténtalo de nuevo.')
            );
            exit;
        }

        $this->finalizarFlujoTicketVerificado($t, $sid);
    }

    public function enviarEnlaceCorreoRevelado()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/ticket'));
            exit;
        }

        $uid = (int) ($_SESSION['recovery_ok_user_id'] ?? 0);
        $email = strtolower(trim((string) ($_SESSION['recovery_ok_email'] ?? '')));
        if ($uid <= 0 || $email === '' || !fv_email_valido($email)) {
            header('Location: ' . url('/ticket') . '?error=' . rawurlencode('Sesión no válida. Completa la verificación otra vez.'));
            exit;
        }
        if ($this->normalizarTipoTicket((string) ($_SESSION['recovery_ok_tipo'] ?? 'correo')) !== 'contrasena') {
            header('Location: ' . url('/ticket') . '?paso=correo&error=' . rawurlencode('Este ticket solo permite consultar el correo. Crea un ticket de contraseña si necesitas cambiarla.'));
            exit;
        }

        $u = Usuario::obtenerPorId($uid);
        if (!$u || strtolower(trim($u->getEmail())) !== $email) {
            $this->limpiarFlujoDni();
            header('Location: ' . url('/ticket') . '?error=' . rawurlencode('No se pudo validar la sesión.'));
            exit;
        }

        if (!$this->enviarCorreoReset($uid, $email)) {
            header('Location: ' . url('/ticket') . '?paso=correo&error=' . rawurlencode('No se pudo enviar el correo. Inténtalo más tarde o contacta con el centro.'));
            exit;
        }

        $this->limpiarFlujoDni();

        header('Location: ' . url('/login') . '?success=' . rawurlencode('Te hemos enviado el enlace para restablecer la contraseña a tu correo. Revisa también spam.'));
        exit;
    }

    public function formularioRestablecer()
    {
        $token = trim((string) ($_GET['token'] ?? ''));
        if (strlen($token) < 32) {
            header('Location: ' . url('/404?motivo=enlace'));
            exit;
        }
        if (Usuario::obtenerPorTokenRecuperacionValido($token) === null) {
            header('Location: ' . url('/404?motivo=enlace'));
            exit;
        }

        $this->renderFrontend('frontend/restablecerContrasena', ['token' => $token]);
    }

    public function guardarNueva()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/ticket'));
            exit;
        }

        $token = trim((string) ($_POST['token'] ?? ''));
        $nueva = (string) ($_POST['clave_nueva'] ?? '');
        $nueva2 = (string) ($_POST['clave_nueva2'] ?? '');

        $row = Usuario::obtenerPorTokenRecuperacionValido($token);
        if ($row === null) {
            header('Location: ' . url('/404?motivo=enlace'));
            exit;
        }

        if ($nueva === '' || $nueva !== $nueva2) {
            header('Location: ' . url('/restablecer-contrasena') . '?' . http_build_query(['token' => $token])
                . '&error=' . rawurlencode('Las contraseñas no coinciden'));
            exit;
        }

        if (!fv_clave_fuerte($nueva)) {
            header(
                'Location: ' . url('/restablecer-contrasena') . '?' . http_build_query(['token' => $token])
                    . '&error=' . rawurlencode(
                        'La contraseña debe tener al menos 16 caracteres e incluir mayúsculas, minúsculas, números y símbolos'
                    )
            );
            exit;
        }

        if (!Usuario::establecerClaveFuerte((int) $row['id'], $nueva)) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('No se pudo guardar la nueva contraseña'));
            exit;
        }

        header('Location: ' . url('/login') . '?success=' . rawurlencode('Contraseña actualizada. Ya puedes iniciar sesión.'));
        exit;
    }
}
