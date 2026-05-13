<?php

require_once "core/Controller.php";
require_once "app/modelos/admin.php";
require_once "app/modelos/solicitud.php";
require_once "app/modelos/monitor.php";
require_once "app/modelos/usuario.php";
require_once "app/modelos/cliente.php";
require_once "app/modelos/admin_config.php";

class AdminControlador extends Controller
{
    private function requireAdmin(): void
    {
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Acceso restringido'));
            exit;
        }
    }

    public function verClientes()
    {
        $this->requireAdmin();
        $this->renderAdmin('admin/verClientes', []);
    }

    public function cancelarPlanCliente(): void
    {
        $this->requireAdmin();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/admin/verClientes'));
            exit;
        }

        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        if ($clienteId <= 0 || !Cliente::cancelarPlanActivo($clienteId)) {
            header('Location: ' . url('/admin/verClientes') . '?error=' . rawurlencode('No se pudo cancelar el plan activo del cliente'));
            exit;
        }

        header('Location: ' . url('/admin/verClientes') . '?success=' . rawurlencode('Plan activo cancelado correctamente'));
        exit;
    }

    public function bloquearCliente(): void
    {
        $this->requireAdmin();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/admin/verClientes'));
            exit;
        }

        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $tipo = (string) ($_POST['tipo'] ?? '');
        $usuarioId = $clienteId > 0 ? Cliente::usuarioIdPorClienteId($clienteId) : null;
        if ($usuarioId === null || !in_array($tipo, ['T', 'P'], true)) {
            header('Location: ' . url('/admin/verClientes') . '?error=' . rawurlencode('Cliente o tipo de baja no válido'));
            exit;
        }

        $hasta = null;
        $motivo = $tipo === 'P' ? 'Baja permanente desde administración' : 'Baja normal solicitada en recepción';

        if (!Usuario::bloquear($usuarioId, $tipo, $hasta, $motivo)) {
            header('Location: ' . url('/admin/verClientes') . '?error=' . rawurlencode('No se pudo aplicar la baja al usuario'));
            exit;
        }

        Cliente::cancelarPlanActivo($clienteId);

        $msg = $tipo === 'P' ? 'Usuario dado de baja permanentemente' : 'Usuario dado de baja normal hasta que solicite la reactivación';
        header('Location: ' . url('/admin/verClientes') . '?success=' . rawurlencode($msg));
        exit;
    }

    public function desbloquearCliente(): void
    {
        $this->requireAdmin();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/admin/verClientes'));
            exit;
        }

        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $usuarioId = $clienteId > 0 ? Cliente::usuarioIdPorClienteId($clienteId) : null;
        if ($usuarioId === null) {
            header('Location: ' . url('/admin/verClientes') . '?error=' . rawurlencode('Cliente no válido'));
            exit;
        }

        $bloqueo = Usuario::estadoBloqueo($usuarioId);
        if (($bloqueo['tipo'] ?? 'N') === 'P') {
            header('Location: ' . url('/admin/verClientes') . '?error=' . rawurlencode('Una baja permanente no se puede reactivar desde el panel'));
            exit;
        }

        if (!Usuario::desbloquear($usuarioId)) {
            header('Location: ' . url('/admin/verClientes') . '?error=' . rawurlencode('No se pudo reactivar el usuario'));
            exit;
        }

        header('Location: ' . url('/admin/verClientes') . '?success=' . rawurlencode('Usuario reactivado correctamente'));
        exit;
    }

    public function verMonitores()
    {
        $this->requireAdmin();
        $this->renderAdmin("admin/verMonitores", []);
    }

    public function formEditarMonitor($id)
    {
        $this->requireAdmin();
        $monitor = Monitor::obtenerPorId($id);
        if ($monitor) {
            $this->renderAdmin("admin/formEditarMonitor", ["monitor" => $monitor]);
        } else {
            echo "Monitor no encontrado.";
        }
    }

    public function editarMonitor()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/admin/verMonitores'));
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $DNI = trim((string) ($_POST['DNI'] ?? ''));
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $apellido1 = trim((string) ($_POST['apellido1'] ?? ''));
        $apellido2 = trim((string) ($_POST['apellido2'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $claveRaw = trim((string) ($_POST['clave'] ?? ''));
        $clave = $claveRaw !== '' ? $claveRaw : null;
        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        $especialidad = trim((string) ($_POST['especialidad'] ?? ''));
        $disponibilidad = trim((string) ($_POST['disponibilidad'] ?? ''));

        if ($id <= 0 || $DNI === '' || $nombre === '' || $apellido1 === '' || $email === '' || $telefono === '' || $especialidad === '' || $disponibilidad === '') {
            header('Location: ' . url('/admin/verMonitores') . '?error=' . rawurlencode('Datos incompletos'));
            exit;
        }

        if (!fv_documento_identidad_es($DNI)) {
            header('Location: ' . url('/admin/verMonitores') . '?error=' . rawurlencode('DNI o NIE no válido'));
            exit;
        }

        if (!fv_email_valido($email)) {
            header('Location: ' . url('/admin/verMonitores') . '?error=' . rawurlencode('Email no válido'));
            exit;
        }

        if (!fv_telefono_es_obligatorio($telefono)) {
            header('Location: ' . url('/admin/verMonitores') . '?error=' . rawurlencode('Teléfono no válido (9 dígitos, ej. 612345678)'));
            exit;
        }

        $claveConf = trim((string) ($_POST['clave_confirmar'] ?? ''));
        if ($clave !== null) {
            if ($clave !== $claveConf) {
                header('Location: ' . url('/admin/verMonitores') . '?error=' . rawurlencode('Las contraseñas no coinciden'));
                exit;
            }
            if (!fv_clave_registro_valida($clave)) {
                header(
                    'Location: ' . url('/admin/verMonitores') . '?error=' . rawurlencode(
                        'La contraseña debe tener al menos 16 caracteres e incluir mayúsculas, minúsculas, números y símbolos'
                    )
                );
                exit;
            }
        }

        if (Monitor::actualizar(
            $id,
            $DNI,
            $nombre,
            $apellido1,
            $apellido2,
            $email,
            $clave,
            $telefono,
            $especialidad,
            $disponibilidad
        )) {
            header('Location: ' . url('/admin/verMonitores'));
            exit;
        }

        header('Location: ' . url('/admin/verMonitores') . '?error=' . rawurlencode('Error al editar monitor'));
        exit;
    }

    public function registrarMonitor()
    {
        $this->requireAdmin();
        $this->renderAdmin("admin/registrarMonitor");
    }
    public function crearMonitor()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/admin/registrarMonitor'));
            exit;
        }

        $DNI = trim((string) ($_POST['DNI'] ?? ''));
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $apellido1 = trim((string) ($_POST['apellido1'] ?? ''));
        $apellido2 = trim((string) ($_POST['apellido2'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $clave = (string) ($_POST['clave'] ?? '');
        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        $especialidad = trim((string) ($_POST['especialidad'] ?? ''));
        $disponibilidad = trim((string) ($_POST['disponibilidad'] ?? ''));

        if ($DNI === '' || $nombre === '' || $apellido1 === '' || $email === '' || $clave === '' || $telefono === '' || $especialidad === '' || $disponibilidad === '') {
            header('Location: ' . url('/admin/registrarMonitor') . '?error=' . rawurlencode('Todos los campos obligatorios deben estar rellenos'));
            exit;
        }

        if (!fv_documento_identidad_es($DNI)) {
            header('Location: ' . url('/admin/registrarMonitor') . '?error=' . rawurlencode('DNI o NIE no válido'));
            exit;
        }

        if (!fv_email_valido($email)) {
            header('Location: ' . url('/admin/registrarMonitor') . '?error=' . rawurlencode('Email no válido'));
            exit;
        }

        $claveConf = (string) ($_POST['clave_confirmar'] ?? '');
        if ($clave !== $claveConf) {
            header('Location: ' . url('/admin/registrarMonitor') . '?error=' . rawurlencode('Las contraseñas no coinciden'));
            exit;
        }
        if (!fv_clave_registro_valida($clave)) {
            header(
                'Location: ' . url('/admin/registrarMonitor') . '?error=' . rawurlencode(
                    'La contraseña debe tener al menos 16 caracteres e incluir mayúsculas, minúsculas, números y símbolos'
                )
            );
            exit;
        }

        if (!fv_telefono_es_obligatorio($telefono)) {
            header('Location: ' . url('/admin/registrarMonitor') . '?error=' . rawurlencode('Teléfono no válido (9 dígitos, ej. 612345678)'));
            exit;
        }

        $datos = [
            'DNI' => $DNI,
            'nombre' => $nombre,
            'apellido1' => $apellido1,
            'apellido2' => $apellido2,
            'email' => $email,
            'clave' => $clave,
            'telefono' => $telefono,
            'especialidad' => $especialidad,
            'disponibilidad' => $disponibilidad,
        ];

        if (Admin::crearMonitor($datos)) {
            header('Location: ' . url('/admin/verMonitores'));
            exit;
        }

        header('Location: ' . url('/admin/registrarMonitor') . '?error=' . rawurlencode('No se pudo crear el monitor'));
        exit;
    }

    public function eliminarMonitor($id)
    {
        $this->requireAdmin();
        if (Monitor::eliminar((int) $id)) {
            header('Location: ' . url('/admin/verMonitores'));
        } else {
            header('Location: ' . url('/admin/verMonitores') . '?error=' . rawurlencode('No se pudo eliminar el monitor'));
        }
        exit;
    }

    public function verSolicitudes()
    {
        $this->requireAdmin();
        $this->renderAdmin("admin/verSolicitudes", []);
    }

    public function verSolicitudesAprobadas()
    {
        $this->requireAdmin();
        $this->renderAdmin("admin/verSolicitudesAprobadas", []);
    }

    public function verSolicitudesRechazadas()
    {
        $this->requireAdmin();
        $this->renderAdmin("admin/verSolicitudesRechazadas", []);
    }

    public function aprobarSolicitud()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/admin/verSolicitudes'));
            exit;
        }

        $id_admin = (int) ($_SESSION['usuario_id'] ?? 0);
        $id = (int) ($_POST['id'] ?? 0);
        $estado = (string) ($_POST['estado'] ?? '');

        if ($id <= 0 || !in_array($estado, ['A', 'R'], true)) {
            header('Location: ' . url('/admin/verSolicitudes') . '?error=' . rawurlencode('Solicitud no válida'));
            exit;
        }

        if (Solicitud::cambiarEstado($id, $estado, $id_admin)) {
            $msg = $estado === 'A' ? 'Solicitud aprobada' : 'Solicitud rechazada';
            header('Location: ' . url('/admin/verSolicitudes') . '?success=' . rawurlencode($msg));
            exit;
        }

        header('Location: ' . url('/admin/verSolicitudes') . '?error=' . rawurlencode('No se pudo actualizar la solicitud'));
        exit;
    }

    public function configSeguridad(): void
    {
        $this->requireAdmin();
        $dias = AdminConfig::getInt('password_rotation_days', 90);
        $idleSec = AdminConfig::getInt('session_idle_timeout_seconds', 2700);
        $idleMin = max(1, (int) round($idleSec / 60));
        $this->renderAdmin('admin/configSeguridad', [
            'dias_rotacion' => $dias,
            'idle_minutos' => $idleMin,
        ]);
    }

    public function guardarConfigSeguridad(): void
    {
        $this->requireAdmin();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/admin/config-seguridad'));
            exit;
        }
        $rawD = trim((string) ($_POST['password_rotation_days'] ?? ''));
        $rawIdle = trim((string) ($_POST['session_idle_minutos'] ?? ''));
        if ($rawD === '' || $rawIdle === '' || !ctype_digit($rawD) || !ctype_digit($rawIdle)) {
            header('Location: ' . url('/admin/config-seguridad') . '?error=' . rawurlencode('Los valores deben ser números enteros (sin decimales ni letras).'));
            exit;
        }
        $d = (int) $rawD;
        $idleMin = (int) $rawIdle;
        if ($d < 0 || $d > 3650) {
            header('Location: ' . url('/admin/config-seguridad') . '?error=' . rawurlencode('Días de rotación no válidos (0–3650)'));
            exit;
        }
        if ($idleMin < 1 || $idleMin > 10080) {
            header('Location: ' . url('/admin/config-seguridad') . '?error=' . rawurlencode('Inactividad: usa entre 1 y 10080 minutos (máx. 1 semana).'));
            exit;
        }
        $idleSec = $idleMin * 60;
        if (!AdminConfig::set('password_rotation_days', (string) $d)) {
            header('Location: ' . url('/admin/config-seguridad') . '?error=' . rawurlencode('No se pudo guardar la rotación de contraseña'));
            exit;
        }
        if (!AdminConfig::set('session_idle_timeout_seconds', (string) $idleSec)) {
            header('Location: ' . url('/admin/config-seguridad') . '?error=' . rawurlencode('No se pudo guardar el tiempo de inactividad'));
            exit;
        }
        header('Location: ' . url('/admin/config-seguridad') . '?success=' . rawurlencode('Configuración de seguridad actualizada'));
        exit;
    }

    public function recuperacionCuentaTickets(): void
    {
        $this->requireAdmin();
        require_once __DIR__ . '/../modelos/recuperacion_cuenta_ticket.php';
        $ticketsPendientes = [];
        $ticketsHistorial = [];
        try {
            $ticketsPendientes = RecuperacionCuentaTicket::listarPendientes();
            $ticketsHistorial = RecuperacionCuentaTicket::listarHistorial(50);
            $firmaTickets = RecuperacionCuentaTicket::firmaDesdeListas($ticketsPendientes, $ticketsHistorial);
        } catch (\Throwable $e) {
            error_log('[Spartum] recuperacionCuentaTickets: ' . $e->getMessage());
            $ticketsPendientes = [];
            $ticketsHistorial = [];
            $firmaTickets = '';
        }
        $this->renderAdmin('admin/recuperacionCuentaTickets', [
            'tickets_pendientes' => $ticketsPendientes,
            'tickets_historial' => $ticketsHistorial,
            'firma_tickets' => $firmaTickets,
        ]);
    }

    public function enviarCorreoRecuperacionTicket(): void
    {
        $this->requireAdmin();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/admin/recuperacion-cuenta'));
            exit;
        }
        require_once dirname(__DIR__, 2) . '/core/helpers/mail_smtp.php';
        require_once __DIR__ . '/../modelos/recuperacion_cuenta_ticket.php';

        $tid = (int) ($_POST['ticket_id'] ?? 0);
        $t = $tid > 0 ? RecuperacionCuentaTicket::obtenerPendientePorId($tid) : null;
        if ($t === null) {
            header('Location: ' . url('/admin/recuperacion-cuenta') . '?error=' . rawurlencode('Solicitud no válida o ya cerrada.'));
            exit;
        }
        if (($t['tipo'] ?? 'correo') !== 'contrasena') {
            header('Location: ' . url('/admin/recuperacion-cuenta') . '?error=' . rawurlencode('Solo los tickets de cambio de contraseña permiten enviar enlace por correo desde el panel.'));
            exit;
        }

        $newToken = RecuperacionCuentaTicket::regenerarAcceso($tid);
        if ($newToken === null) {
            header('Location: ' . url('/admin/recuperacion-cuenta') . '?error=' . rawurlencode('No se pudo preparar el enlace.'));
            exit;
        }

        $u = Usuario::obtenerPorId((int) $t['usuario_id']);
        if (!$u) {
            header('Location: ' . url('/admin/recuperacion-cuenta') . '?error=' . rawurlencode('Usuario no encontrado.'));
            exit;
        }

        $email = strtolower(trim($u->getEmail()));
        if (!fv_email_valido($email)) {
            header('Location: ' . url('/admin/recuperacion-cuenta') . '?error=' . rawurlencode('El usuario no tiene un email válido.'));
            exit;
        }

        $sid = (int) $t['id'];
        $codigo = (string) $t['codigo'];
        $linkAcceso = app_url_absolute('/recuperar-contrasena/ticket/' . $newToken);
        $linkCodigo = app_url_absolute('/ticket?' . http_build_query(['paso' => 'codigo', 'solicitud' => $sid]));

        $nombre = trim($u->getNombre() . ' ' . $u->getApellido1());
        $safeName = htmlspecialchars($nombre !== '' ? $nombre : 'usuario', ENT_QUOTES, 'UTF-8');

        $asunto = 'Cambiar contraseña en Spartum — ticket n.º ' . $sid;
        $html = '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"></head><body style="font-family:system-ui,sans-serif;line-height:1.5;color:#222;max-width:560px;margin:0 auto;padding:24px;">'
            . '<p>Hola ' . $safeName . ',</p>'
            . '<p>Tras verificar tu identidad, aquí tienes los datos para <strong>cambiar tu contraseña</strong> en Spartum '
            . '(ticket <strong>#' . (int) $sid . '</strong>). Si ya te dieron el código en recepción, es el mismo que aparece abajo. El enlace caduca en los próximos días; pide uno nuevo en recepción si caduca.</p>'
            . '<p style="margin:20px 0;"><strong>Código (también válido si te lo dictaron en el centro):</strong><br>'
            . '<span style="font-family:ui-monospace,monospace;font-size:1.25rem;letter-spacing:0.08em;">'
            . htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8') . '</span></p>'
            . '<p style="margin:20px 0;"><a href="' . htmlspecialchars($linkAcceso, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;background:#e85d04;color:#fff;text-decoration:none;padding:12px 22px;border-radius:8px;font-weight:600;">Continuar con enlace seguro</a></p>'
            . '<p style="font-size:13px;color:#555;">Alternativa: abre <a href="' . htmlspecialchars($linkCodigo, ENT_QUOTES, 'UTF-8') . '">esta página</a> e introduce el código.</p>'
            . '<p style="font-size:13px;color:#555;">Si no solicitaste esto, ignora el mensaje o contacta con el centro.</p>'
            . '</body></html>';
        $texto = "Hola {$nombre},\n\nTicket #{$sid}. Código: {$codigo}\n\nEnlace directo:\n{$linkAcceso}\n\nPágina para introducir el código:\n{$linkCodigo}\n";

        $err = null;
        if (!gp_mail_send($email, $asunto, $html, $texto, $err)) {
            error_log('[Spartum admin recuperacion] ' . ($err ?? 'mail fail'));
            header('Location: ' . url('/admin/recuperacion-cuenta') . '?error=' . rawurlencode('No se pudo enviar el correo. Revisa la configuración SMTP.'));
            exit;
        }

        header('Location: ' . url('/admin/recuperacion-cuenta') . '?success=' . rawurlencode('Correo enviado a ' . $email . '. El código del ticket sigue siendo el mismo para quien continúe en recepción.'));
        exit;
    }

    public function cerrarTicketRecuperacion(): void
    {
        $this->requireAdmin();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/admin/recuperacion-cuenta'));
            exit;
        }
        require_once __DIR__ . '/../modelos/recuperacion_cuenta_ticket.php';

        $tid = (int) ($_POST['ticket_id'] ?? 0);
        if ($tid <= 0) {
            header('Location: ' . url('/admin/recuperacion-cuenta') . '?error=' . rawurlencode('Solicitud no válida.'));
            exit;
        }

        if (!RecuperacionCuentaTicket::cerrarPorAdministrador($tid)) {
            header('Location: ' . url('/admin/recuperacion-cuenta') . '?error=' . rawurlencode('No se pudo cerrar el ticket (quizá ya estaba cerrado o caducado).'));
            exit;
        }

        header('Location: ' . url('/admin/recuperacion-cuenta') . '?success=' . rawurlencode('Ticket #' . $tid . ' cerrado. Ya no aparecerá en abiertos.'));
        exit;
    }
}
