<?php

require_once "core/Controller.php";
require_once "app/modelos/usuario.php";
require_once "app/modelos/cliente.php";
require_once "app/modelos/cliente_subscripcion.php";

class UsuarioControlador extends Controller
{
    private function requireClienteSesion(): int
    {
        $this->redirigirFisioFueraPortal();
        if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'cliente') {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Inicia sesión como socio para gestionar tu cuenta.'));
            exit;
        }

        return (int) $_SESSION['usuario_id'];
    }

    private function cerrarSesionActual(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }
        session_destroy();
    }
    /**
     * Envío de bienvenida; devuelve true solo si SMTP aceptó el mensaje.
     *
     * @param string|null $errorPublic texto seguro para mostrar si falla
     */
    private function enviarCorreoConfirmacionRegistro(
        string $emailDestino,
        string $nombre,
        string $apellido1,
        string $tokenConfirmacion,
        ?string &$errorPublic = null
    ): bool {
        require_once dirname(__DIR__, 2) . '/core/helpers/mail_smtp.php';

        $confirmUrl = app_url_absolute('/confirmar-cuenta?' . http_build_query(['token' => $tokenConfirmacion]));
        $loginUrl = app_url_absolute('/login');
        $nombreCompleto = trim($nombre . ' ' . $apellido1);
        $asunto = 'Confirma tu cuenta en Spartum';

        $safeName = htmlspecialchars($nombreCompleto !== '' ? $nombreCompleto : $nombre, ENT_QUOTES, 'UTF-8');
        $cuerpoHtml = '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><title>Confirmar cuenta</title></head><body style="font-family:system-ui,Segoe UI,sans-serif;line-height:1.5;color:#222;max-width:560px;margin:0 auto;padding:24px;">'
            . '<p style="font-size:16px;">Hola ' . $safeName . ',</p>'
            . '<p>Gracias por darte de alta en <strong>Spartum</strong>. Para activar tu cuenta y poder reservar clases, confirma que este correo es tuyo. El enlace caduca a las <strong>48 horas</strong>.</p>'
            . '<p style="margin:28px 0;"><a href="' . htmlspecialchars($confirmUrl, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;background:#e85d04;color:#fff;text-decoration:none;padding:12px 22px;border-radius:8px;font-weight:600;">Confirmar mi cuenta</a></p>'
            . '<p style="font-size:13px;color:#555;">Si el botón no funciona, copia y pega esta URL en el navegador:<br><span style="word-break:break-all;">' . htmlspecialchars($confirmUrl, ENT_QUOTES, 'UTF-8') . '</span></p>'
            . '<p>Cuando esté activa, podrás entrar aquí: <a href="' . htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') . '</a></p>'
            . '<p style="font-size:13px;color:#666;">Si no has sido tú, ignora este mensaje.</p>'
            . '<p>Un saludo,<br><strong>Spartum</strong></p>'
            . '</body></html>';

        $cuerpoTexto = "Hola {$nombreCompleto},\n\n"
            . "Activa tu cuenta en Spartum (enlace válido 48 horas):\n{$confirmUrl}\n\n"
            . "Sin confirmar no podrás iniciar sesión.\n"
            . "Login: {$loginUrl}\n\n"
            . "Si no te registraste, ignora este correo.\n\n"
            . "— Spartum\n";

        return gp_mail_send($emailDestino, $asunto, $cuerpoHtml, $cuerpoTexto, $errorPublic);
    }

    public function confirmarCuenta()
    {
        $token = trim((string) ($_GET['token'] ?? ''));
        if ($token === '') {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Enlace de confirmación no válido.'));
            exit;
        }

        $email = Usuario::confirmarEmailConToken($token);
        if ($email === null) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('El enlace ha caducado o no es válido. Regístrate de nuevo o contacta con el centro.'));
            exit;
        }

        header('Location: ' . url('/login') . '?success=' . rawurlencode('Cuenta activada. Ya puedes iniciar sesión con tu email y contraseña.'));
        exit;
    }

    public function registrar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/login'));
            exit;
        }

        $DNI = trim((string) ($_POST['DNI'] ?? ''));
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $apellido1 = trim((string) ($_POST['apellido1'] ?? ''));
        $apellido2 = trim((string) ($_POST['apellido2'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $clave = (string) ($_POST['clave'] ?? '');
        $clave2 = (string) ($_POST['clave_confirmar'] ?? '');
        $telefono = trim((string) ($_POST['telefono'] ?? ''));

        if ($DNI === '' || $nombre === '' || $apellido1 === '' || $email === '' || $clave === '') {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Faltan campos obligatorios'));
            exit;
        }

        if ($clave !== $clave2) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Las contraseñas no coinciden'));
            exit;
        }

        if (!fv_documento_identidad_es($DNI)) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('DNI o NIE no válido'));
            exit;
        }

        if (!fv_email_valido($email)) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Email no válido'));
            exit;
        }

        if (!fv_clave_registro_valida($clave)) {
            header(
                'Location: ' . url('/login') . '?error=' . rawurlencode(
                    'La contraseña debe tener al menos 16 caracteres e incluir mayúsculas, minúsculas, números y símbolos'
                )
            );
            exit;
        }

        if (!fv_telefono_es_opcional($telefono)) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Teléfono no válido (9 dígitos españoles o déjalo vacío)'));
            exit;
        }

        $usuario = new Usuario(
            $DNI,
            $nombre,
            $apellido1,
            $apellido2,
            $email,
            $clave,
            $telefono
        );

        $pdo = BasedeDatos::Conectar();
        if (!$pdo) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Error temporal. Inténtalo de nuevo más tarde.'));
            exit;
        }

        $mailHost = trim((string) ($_ENV['MAIL_HOST'] ?? getenv('MAIL_HOST') ?: ''));

        /* Sin SMTP (p. ej. XAMPP local): cuenta activa de inmediato; con SMTP, confirmación por correo. */
        if ($mailHost === '') {
            try {
                $pdo->beginTransaction();
            } catch (\Throwable $e) {
                header('Location: ' . url('/login') . '?error=' . rawurlencode('Error temporal. Inténtalo de nuevo más tarde.'));
                exit;
            }
            if (!$usuario->persistirComoNuevoCliente($pdo, null, null)) {
                try {
                    $pdo->rollBack();
                } catch (\Throwable $e) {
                }
                header('Location: ' . url('/login') . '?error=' . rawurlencode('No se pudo registrar (¿email o DNI ya existen?)'));
                exit;
            }
            try {
                $pdo->commit();
            } catch (\Throwable $e) {
                try {
                    $pdo->rollBack();
                } catch (\Throwable $e2) {
                }
                header('Location: ' . url('/login') . '?error=' . rawurlencode('Error al finalizar el registro. Inténtalo de nuevo.'));
                exit;
            }
            header(
                'Location: ' . url('/login') . '?success=' . rawurlencode(
                    'Cuenta creada. No hay servidor de correo configurado: tu cuenta ya está activa y puedes iniciar sesión.'
                )
            );
            exit;
        }

        try {
            $pdo->beginTransaction();
        } catch (\Throwable $e) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Error temporal. Inténtalo de nuevo más tarde.'));
            exit;
        }

        $tokenPlano = bin2hex(random_bytes(32));
        $expira = (new DateTimeImmutable('+48 hours'))->format('Y-m-d H:i:s');

        if (!$usuario->persistirComoNuevoCliente($pdo, $tokenPlano, $expira)) {
            $pdo->rollBack();
            header('Location: ' . url('/login') . '?error=' . rawurlencode('No se pudo registrar (¿email o DNI ya existen?)'));
            exit;
        }

        $mailErr = null;
        if (!$this->enviarCorreoConfirmacionRegistro($email, $nombre, $apellido1, $tokenPlano, $mailErr)) {
            $pdo->rollBack();
            $msg = $mailErr !== '' && $mailErr !== null
                ? ('No se pudo completar el registro: ' . $mailErr . ' Revisa también la carpeta de spam o prueba más tarde.')
                : 'No se pudo enviar el correo de confirmación. No se ha creado la cuenta; revisa tu email o inténtalo más tarde.';
            header('Location: ' . url('/login') . '?error=' . rawurlencode($msg));
            exit;
        }

        try {
            $pdo->commit();
        } catch (\Throwable $e) {
            try {
                $pdo->rollBack();
            } catch (\Throwable $e2) {
            }
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Error al finalizar el registro. Inténtalo de nuevo.'));
            exit;
        }

        header('Location: ' . url('/login') . '?success=' . rawurlencode('Registro correcto. Revisa tu correo (usa una dirección real) y pulsa el enlace para activar la cuenta; hasta entonces no podrás iniciar sesión.'));
        exit;
    }

    public function formEditarCliente()
    {
        $this->redirigirFisioFueraPortal();
        $uid = (int) ($_SESSION['usuario_id'] ?? 0);
        $cliente_id = $uid > 0 ? Cliente::IdClientePorUsuarioId($uid) : null;

        if (!$cliente_id) {
            $rol = (string) ($_SESSION['rol'] ?? '');
            if ($rol === 'admin' || $rol === 'monitor') {
                header('Location: ' . url('/cuenta/perfil'));
                exit;
            }
            header('Location: ' . url('/login') . '?error=' . rawurlencode('No se encontró tu ficha de socio. Contacta con el centro.'));
            exit;
        }
        $cliente = Cliente::obtenerPorId($cliente_id);
        if ($cliente) {
            $this->renderFrontend("cliente/formEditarCliente", ["cliente" => $cliente]);
        } else {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('No se encontró tu ficha de socio.'));
            exit;
        }
    }

    public function formCuentaPerfil(): void
    {
        $this->redirigirFisioFueraPortal();
        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Debes iniciar sesión'));
            exit;
        }
        $rol = (string) ($_SESSION['rol'] ?? '');
        if ($rol === 'cliente') {
            header('Location: ' . url('/clientes/editar'));
            exit;
        }
        if (!in_array($rol, ['admin', 'monitor'], true)) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Esta página no está disponible para tu rol.'));
            exit;
        }

        $u = Usuario::obtenerPorId((int) $_SESSION['usuario_id']);
        if (!$u) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Usuario no encontrado.'));
            exit;
        }

        $perfil = [
            'usuario_id' => (int) $u->getId(),
            'DNI' => (string) $u->getDNI(),
            'nombre' => (string) $u->getNombre(),
            'apellido1' => (string) $u->getApellido1(),
            'apellido2' => (string) $u->getApellido2(),
            'email' => (string) $u->getEmail(),
            'telefono' => (string) $u->getTelefono(),
        ];
        $cancelHref = $rol === 'admin' ? url('/admin') : url('/inicioMonitor');

        $this->renderFrontend('frontend/formEditarPerfilCuenta', [
            'perfil' => $perfil,
            'cancelHref' => $cancelHref,
        ]);
    }

    public function guardarCuentaPerfil(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/cuenta/perfil'));
            exit;
        }

        $this->redirigirFisioFueraPortal();

        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Debes iniciar sesión'));
            exit;
        }

        $rol = (string) ($_SESSION['rol'] ?? '');
        if ($rol === 'cliente') {
            header('Location: ' . url('/clientes/editar'));
            exit;
        }
        if (!in_array($rol, ['admin', 'monitor'], true)) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Esta acción no está disponible para tu rol.'));
            exit;
        }

        $idPost = (int) ($_POST['id'] ?? 0);
        if ($idPost !== (int) $_SESSION['usuario_id']) {
            $dest = $rol === 'admin' ? url('/admin') : url('/inicioMonitor');
            header('Location: ' . $dest . '?error=' . rawurlencode('No puedes modificar otro usuario'));
            exit;
        }

        $DNI = trim((string) ($_POST['DNI'] ?? ''));
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $apellido1 = trim((string) ($_POST['apellido1'] ?? ''));
        $apellido2 = trim((string) ($_POST['apellido2'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        $claveRaw = trim((string) ($_POST['clave'] ?? ''));
        $claveConfirm = trim((string) ($_POST['clave_confirmar'] ?? ''));
        $clave = $claveRaw !== '' ? $claveRaw : null;

        if ($DNI === '' || $nombre === '' || $apellido1 === '' || $email === '') {
            header('Location: ' . url('/cuenta/perfil') . '?error=' . rawurlencode('Faltan campos obligatorios'));
            exit;
        }

        if (!fv_documento_identidad_es($DNI)) {
            header('Location: ' . url('/cuenta/perfil') . '?error=' . rawurlencode('DNI o NIE no válido'));
            exit;
        }

        if (!fv_email_valido($email)) {
            header('Location: ' . url('/cuenta/perfil') . '?error=' . rawurlencode('Email no válido'));
            exit;
        }

        if ($clave !== null) {
            if ($clave !== $claveConfirm) {
                header('Location: ' . url('/cuenta/perfil') . '?error=' . rawurlencode('Las contraseñas nuevas no coinciden'));
                exit;
            }
            if (!fv_clave_registro_valida($clave)) {
                header(
                    'Location: ' . url('/cuenta/perfil') . '?error=' . rawurlencode(
                        'La nueva contraseña debe tener al menos 16 caracteres e incluir mayúsculas, minúsculas, números y símbolos'
                    )
                );
                exit;
            }
        }

        if (!fv_telefono_es_opcional($telefono)) {
            header('Location: ' . url('/cuenta/perfil') . '?error=' . rawurlencode('Teléfono no válido (9 dígitos españoles o déjalo vacío)'));
            exit;
        }

        if (!Usuario::actualizar(
            (string) $idPost,
            $DNI,
            $nombre,
            $apellido1,
            $apellido2,
            $email,
            $clave,
            $telefono
        )) {
            header('Location: ' . url('/cuenta/perfil') . '?error=' . rawurlencode('Error al guardar los cambios'));
            exit;
        }

        $_SESSION['nombre'] = $nombre;
        $_SESSION['email'] = $email;
        if ($clave !== null && fv_clave_fuerte($clave)) {
            unset($_SESSION['password_must_change']);
        }
        $okDest = $rol === 'admin' ? url('/admin') : url('/inicioMonitor');
        header('Location: ' . $okDest . '?success=' . rawurlencode('Perfil actualizado correctamente'));
        exit;
    }

    public function editarCliente()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/clientes/editar'));
            exit;
        }

        $this->redirigirFisioFueraPortal();

        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Debes iniciar sesión'));
            exit;
        }

        $uidSesion = (int) $_SESSION['usuario_id'];
        if (!Cliente::IdClientePorUsuarioId($uidSesion)) {
            header('Location: ' . url('/cuenta/perfil'));
            exit;
        }

        $idPost = (int) ($_POST['id'] ?? 0);
        if ($idPost !== (int) $_SESSION['usuario_id']) {
            header('Location: ' . url('/inicioUsuario') . '?error=' . rawurlencode('No puedes modificar otro usuario'));
            exit;
        }

        $DNI = trim((string) ($_POST['DNI'] ?? ''));
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $apellido1 = trim((string) ($_POST['apellido1'] ?? ''));
        $apellido2 = trim((string) ($_POST['apellido2'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        $claveRaw = trim((string) ($_POST['clave'] ?? ''));
        $claveConfirm = trim((string) ($_POST['clave_confirmar'] ?? ''));
        $clave = $claveRaw !== '' ? $claveRaw : null;

        if ($DNI === '' || $nombre === '' || $apellido1 === '' || $email === '') {
            header('Location: ' . url('/clientes/editar') . '?error=' . rawurlencode('Faltan campos obligatorios'));
            exit;
        }

        if (!fv_documento_identidad_es($DNI)) {
            header('Location: ' . url('/clientes/editar') . '?error=' . rawurlencode('DNI o NIE no válido'));
            exit;
        }

        if (!fv_email_valido($email)) {
            header('Location: ' . url('/clientes/editar') . '?error=' . rawurlencode('Email no válido'));
            exit;
        }

        if ($clave !== null) {
            if ($clave !== $claveConfirm) {
                header('Location: ' . url('/clientes/editar') . '?error=' . rawurlencode('Las contraseñas nuevas no coinciden'));
                exit;
            }
            if (!fv_clave_registro_valida($clave)) {
                header(
                    'Location: ' . url('/clientes/editar') . '?error=' . rawurlencode(
                        'La nueva contraseña debe tener al menos 16 caracteres e incluir mayúsculas, minúsculas, números y símbolos'
                    )
                );
                exit;
            }
        }

        if (!fv_telefono_es_opcional($telefono)) {
            header('Location: ' . url('/clientes/editar') . '?error=' . rawurlencode('Teléfono no válido (9 dígitos españoles o déjalo vacío)'));
            exit;
        }

        if (!Usuario::actualizar(
            (string) $idPost,
            $DNI,
            $nombre,
            $apellido1,
            $apellido2,
            $email,
            $clave,
            $telefono
        )) {
            header('Location: ' . url('/clientes/editar') . '?error=' . rawurlencode('Error al guardar los cambios'));
            exit;
        }

        $_SESSION['nombre'] = $nombre;
        $_SESSION['email'] = $email;
        if ($clave !== null && fv_clave_fuerte($clave)) {
            unset($_SESSION['password_must_change']);
        }
        header('Location: ' . url('/inicioUsuario') . '?success=' . rawurlencode('Perfil actualizado correctamente'));
        exit;
    }

    public function darseDeBaja()
    {
        $usuarioId = $this->requireClienteSesion();
        $bloqueo = Usuario::estadoBloqueo($usuarioId);
        if (!empty($bloqueo['bloqueado'])) {
            $msg = ($bloqueo['tipo'] ?? '') === 'P'
                ? 'Tu cuenta ya está dada de baja permanentemente. Contacta con recepción.'
                : 'Tu cuenta ya está dada de baja. Usa el formulario de reactivación o contacta con recepción.';
            header('Location: ' . url('/login') . '?error=' . rawurlencode($msg));
            exit;
        }

        $plan = ClienteSubscripcion::obtenerActivaPorUsuarioId($usuarioId);
        $this->renderFrontend('frontend/darseDeBaja', [
            'plan_activo' => $plan,
            'tiene_plan' => $plan !== null,
        ]);
    }

    public function confirmarDarseDeBaja()
    {
        $usuarioId = $this->requireClienteSesion();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/darse-de-baja'));
            exit;
        }

        if (empty($_POST['acepto_terminos'])) {
            header('Location: ' . url('/darse-de-baja') . '?error=' . rawurlencode('Debes leer y aceptar las condiciones de baja para continuar.'));
            exit;
        }

        $bloqueo = Usuario::estadoBloqueo($usuarioId);
        if (!empty($bloqueo['bloqueado'])) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Tu cuenta ya está dada de baja.'));
            exit;
        }

        $clienteId = (int) (Cliente::IdClientePorUsuarioId($usuarioId) ?? 0);
        if ($clienteId <= 0) {
            header('Location: ' . url('/darse-de-baja') . '?error=' . rawurlencode('No se encontró tu perfil de socio.'));
            exit;
        }

        if (!Usuario::bloquear($usuarioId, 'T', null, 'Baja normal solicitada por el usuario')) {
            header('Location: ' . url('/darse-de-baja') . '?error=' . rawurlencode('No se pudo procesar la baja. Inténtalo de nuevo o contacta con recepción.'));
            exit;
        }

        Cliente::cancelarPlanActivo($clienteId);
        $this->cerrarSesionActual();

        header(
            'Location: ' . url('/login') . '?success=' . rawurlencode(
                'Tu cuenta ha sido dada de baja. Para volver a acceder deberás crear un ticket de reactivación y acudir a recepción. Si te das de alta de nuevo, no conservarás ningún plan anterior.'
            )
        );
        exit;
    }
}
