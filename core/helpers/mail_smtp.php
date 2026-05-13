<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * SMTP con variables MAIL_* (.env cargado desde index.php).
 */

function gp_mail_env(string $key, string $default = ''): string
{
    $v = $_ENV[$key] ?? getenv($key);
    if ($v === false || $v === null) {
        return $default;
    }

    return trim((string) $v);
}

function gp_mail_debug_enabled(): bool
{
    foreach (['MAIL_MAIL_DEBUG', 'MAIL_SMTP_DEBUG', 'MAIL_DEBUG'] as $k) {
        $v = strtolower(gp_mail_env($k));
        if (in_array($v, ['1', 'true', 'yes', 'on'], true)) {
            return true;
        }
    }

    return false;
}

/**
 * Mensaje legible sin filtrar credenciales.
 */
function gp_mail_error_para_usuario(\Throwable $e): string
{
    $msg = $e->getMessage();
    if (preg_match('/535|authenticate|authentication failed|incorrect authentication/i', $msg) === 1) {
        return 'El servidor de correo rechazó usuario o contraseña (MAIL_USERNAME / MAIL_PASSWORD).';
    }
    if (preg_match('/connection refused|could not connect|connection timed out|network is unreachable/i', $msg) === 1) {
        return 'No hay conexión con el servidor SMTP (revisa MAIL_HOST y MAIL_PORT, o el firewall).';
    }
    if (preg_match('/certificate|verify peer|ssl|tls/i', $msg) === 1) {
        return 'Fallo SSL/TLS con el servidor SMTP. Ajusta MAIL_ENCRYPTION y, solo en desarrollo, MAIL_SSL_VERIFY_PEER=0.';
    }

    return 'No se ha podido enviar el correo. Revisa la configuración SMTP del servidor.';
}

function gp_mail_configure_phpmailer(PHPMailer $mail): void
{
    $host = gp_mail_env('MAIL_HOST');
    $mail->isSMTP();
    $mail->Host = $host;
    $timeoutStr = gp_mail_env('MAIL_SMTP_TIMEOUT', '25');
    $timeout = max(5, min(120, $timeoutStr !== '' ? (int) $timeoutStr : 25));
    $mail->Timeout = $timeout;
    $mail->SMTPKeepAlive = false;

    $user = gp_mail_env('MAIL_USERNAME');
    $password = gp_mail_env('MAIL_PASSWORD');

    $authMode = strtolower(gp_mail_env('MAIL_SMTP_AUTH'));
    if (in_array($authMode, ['0', 'false', 'no', 'off'], true)) {
        $mail->SMTPAuth = false;
    } elseif (in_array($authMode, ['1', 'true', 'yes', 'on'], true)) {
        $mail->SMTPAuth = true;
        $mail->Username = $user;
        $mail->Password = $password;
    } else {
        // Automático: servicios locales suelen funcionar sin auth.
        $mail->SMTPAuth = $user !== '';
        $mail->Username = $mail->SMTPAuth ? $user : '';
        $mail->Password = $mail->SMTPAuth ? $password : '';
    }

    $mail->Port = (int) gp_mail_env('MAIL_PORT', '587');

    $encryption = strtolower(gp_mail_env('MAIL_ENCRYPTION', 'tls'));
    if ($encryption === 'ssl') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } elseif ($encryption === 'tls') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    } else {
        $mail->SMTPSecure = '';
        $mail->SMTPAutoTLS = false;
    }

    $vPeer = strtolower(gp_mail_env('MAIL_SSL_VERIFY_PEER', '1'));
    if ($vPeer === '0' || $vPeer === 'false' || $vPeer === 'no') {
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
    }

    if (gp_mail_debug_enabled()) {
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->Debugoutput = static function (string $str, int $level): void {
            error_log('[SMTP] ' . trim(preg_replace('/\s+/u', ' ', $str)));
        };
    }
}

/**
 * Envía HTML + texto alternativo.
 *
 * @param string|null $outErrorPublic texto seguro para mostrar al usuario
 */
function gp_mail_send(string $to, string $subject, string $htmlBody, string $textBody, ?string &$outErrorPublic = null): bool
{
    require_once __DIR__ . '/form_validacion.php';

    $outErrorPublic = null;

    $host = gp_mail_env('MAIL_HOST');
    if ($host === '') {
        $outErrorPublic = 'Falta configurar MAIL_HOST.';

        return false;
    }
    if (!fv_email_valido($to)) {
        $outErrorPublic = 'Dirección de correo destino no válida.';

        return false;
    }

    if (!class_exists(PHPMailer::class)) {
        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
    }

    $mail = new PHPMailer(true);
    try {
        gp_mail_configure_phpmailer($mail);

        $fromName = gp_mail_env('MAIL_FROM_NAME', 'Spartum');
        $fromAddr = gp_mail_env('MAIL_FROM_ADDRESS');
        $smtpUser = gp_mail_env('MAIL_USERNAME');
        if (($fromAddr === '' || strcasecmp($fromAddr, 'no-reply@localhost') === 0)
            && $smtpUser !== '' && fv_email_valido($smtpUser)) {
            $fromAddr = $smtpUser;
        }
        if ($fromAddr === '') {
            $fromAddr = 'no-reply@localhost';
        }
        $mail->setFrom($fromAddr, $fromName !== '' ? $fromName : 'Spartum');
        $reply = gp_mail_env('MAIL_REPLY_TO');
        if ($reply !== '' && fv_email_valido($reply)) {
            $mail->addReplyTo($reply);
        } elseif ($smtpUser !== '' && fv_email_valido($smtpUser)) {
            $mail->addReplyTo($smtpUser);
        }
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = $textBody;
        $mail->send();

        return true;
    } catch (\Throwable $e) {
        error_log('[Spartum mail] ' . $e->getMessage());
        $outErrorPublic = gp_mail_error_para_usuario($e);

        return false;
    }
}
