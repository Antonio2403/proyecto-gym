<?php

require_once "core/Controller.php";
require_once "app/modelos/usuario.php";
require_once "app/modelos/cliente.php";

class UsuarioControlador extends Controller
{
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
        $asunto = 'Spartum — confirma tu cuenta';

        $cuerpoHtml = '<p>Hola ' . htmlspecialchars($nombreCompleto ?: $nombre, ENT_QUOTES, 'UTF-8') . ',</p>'
            . '<p>Gracias por registrarte en <strong>Spartum</strong>. Para activar tu cuenta y poder iniciar sesión, confirma tu correo pulsando el siguiente enlace (válido 48 horas):</p>'
            . '<p><a href="' . htmlspecialchars($confirmUrl, ENT_QUOTES, 'UTF-8')
            . '">Confirmar mi cuenta</a></p>'
            . '<p>Si el enlace no funciona, copia y pega esta dirección en el navegador:<br>'
            . '<span style="word-break:break-all;">' . htmlspecialchars($confirmUrl, ENT_QUOTES, 'UTF-8') . '</span></p>'
            . '<p>Después podrás entrar en: <a href="' . htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') . '">inicio de sesión</a></p>'
            . '<p>Un saludo,<br>El equipo de Spartum</p>';

        $cuerpoTexto = "Hola {$nombreCompleto},\n\n"
            . "Confirma tu cuenta en Spartum abriendo este enlace (48 horas):\n{$confirmUrl}\n\n"
            . "Hasta confirmar, no podrás iniciar sesión.\n"
            . "Después entra en: {$loginUrl}\n\n"
            . "El equipo de Spartum\n";

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
        $telefono = trim((string) ($_POST['telefono'] ?? ''));

        if ($DNI === '' || $nombre === '' || $apellido1 === '' || $email === '' || $clave === '') {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Faltan campos obligatorios'));
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
            header('Location: ' . url('/login') . '?error=' . rawurlencode('La contraseña debe tener al menos 8 caracteres'));
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

        $mailHost = trim((string) ($_ENV['MAIL_HOST'] ?? getenv('MAIL_HOST') ?: ''));
        if ($mailHost === '') {
            header(
                'Location: ' . url('/login') . '?error=' . rawurlencode(
                    'El registro requiere enviar un correo de confirmación y el servicio no está disponible. Inténtalo más tarde o contacta con el centro.'
                )
            );
            exit;
        }

        $pdo = BasedeDatos::Conectar();
        if (!$pdo) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Error temporal. Inténtalo de nuevo más tarde.'));
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

        header('Location: ' . url('/login') . '?success=' . rawurlencode('Registro correcto. Revisa tu correo y pulsa el enlace para activar la cuenta; hasta entonces no podrás iniciar sesión.'));
        exit;
    }

    public function formEditarCliente()
    {
        $this->redirigirFisioFueraPortal();
        $cliente_id = Cliente::IdClientePorUsuarioId($_SESSION['usuario_id']);

        if (!$cliente_id) {
            die("Cliente no encontrado");
        }
        $cliente = Cliente::obtenerPorId($cliente_id);
        if ($cliente) {
            $this->renderFrontend("cliente/formEditarCliente", ["cliente" => $cliente]);
        } else {
            echo "Cliente no encontrado.";
        }
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

        if ($clave !== null && !fv_clave_registro_valida($clave)) {
            header('Location: ' . url('/clientes/editar') . '?error=' . rawurlencode('La nueva contraseña debe tener al menos 8 caracteres'));
            exit;
        }

        if (Usuario::actualizar(
            (string) $idPost,
            $DNI,
            $nombre,
            $apellido1,
            $apellido2,
            $email,
            $clave,
            $telefono
        )) {
            $_SESSION['nombre'] = $nombre;
            $_SESSION['email'] = $email;
            header('Location: ' . url('/inicioUsuario') . '?success=' . rawurlencode('Perfil actualizado correctamente'));
            exit;
        }

        header('Location: ' . url('/clientes/editar') . '?error=' . rawurlencode('Error al guardar los cambios'));
        exit;
    }

    public function darseDeBaja()
    {
        $this->redirigirFisioFueraPortal();
        $this->renderFrontend('frontend/darseDeBaja');
    }
}
