<?php

declare(strict_types=1);

/**
 * Arranque de seguridad antes de session_start() y cabeceras HTTP de refuerzo.
 * Pensado para hosting de prueba sin HTTPS: cookies sin Secure, SameSite=Lax, HttpOnly.
 * Con HTTPS (o proxy TLS con APP_TRUST_PROXY): Secure y HSTS cuando aplique.
 */

function gp_env_bool(string $key, bool $default = false): bool
{
    $v = $_ENV[$key] ?? getenv($key);
    if ($v === false || $v === null || $v === '') {
        return $default;
    }
    $s = strtolower(trim((string) $v));

    return in_array($s, ['1', 'true', 'yes', 'on'], true);
}

/**
 * HTTPS efectivo (incluye proxy que termina TLS si APP_TRUST_PROXY=1).
 */
function gp_request_is_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && (string) $_SERVER['HTTPS'] !== 'off') {
        return true;
    }
    if (isset($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443') {
        return true;
    }
    if (gp_env_bool('APP_TRUST_PROXY', false)) {
        $xfp = strtolower(trim((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')));
        if ($xfp === 'https') {
            return true;
        }
    }

    return false;
}

function gp_session_cookie_secure(): bool
{
    if (gp_env_bool('APP_FORCE_SECURE_COOKIES', false)) {
        return true;
    }

    return gp_request_is_https();
}

/**
 * Debe llamarse una sola vez, antes de session_start().
 */
function gp_configure_session_ini(): void
{
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');

    $secure = gp_session_cookie_secure();
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        ini_set('session.cookie_secure', $secure ? '1' : '0');
    }
}

/**
 * Cabeceras que no rompen CDNs (Bootstrap, fuentes) ni Stripe en el cliente.
 * Llamar tras session_start() y antes de cualquier salida HTML.
 */
function gp_send_security_headers(): void
{
    if (headers_sent()) {
        return;
    }

    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header(
        'Permissions-Policy: accelerometer=(), camera=(), geolocation=(), gyroscope=(), '
        . 'magnetometer=(), microphone=(), payment=(self), usb=()'
    );

    if (gp_request_is_https()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
