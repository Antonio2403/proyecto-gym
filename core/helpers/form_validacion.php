<?php

declare(strict_types=1);

/**
 * Validación en servidor (fuente de verdad junto a la BD). Resumen de capas: VALIDACIONES_CAPAS.txt
 * Token CSRF de sesión para formularios y peticiones fetch mutadoras.
 */
function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_validate(?string $token): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $expected = $_SESSION['csrf_token'] ?? '';

    return is_string($expected) && $expected !== '' && is_string($token) && hash_equals($expected, $token);
}

function csrf_validate_request(): bool
{
    $token = $_POST['csrf_token'] ?? null;
    if (!is_string($token) || $token === '') {
        $header = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $token = is_string($header) ? $header : null;
    }

    return csrf_validate($token);
}

/**
 * Email RFC básico vía filtro de PHP.
 */
function fv_email_valido(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * DNI (8 dígitos + letra) o NIE (X/Y/Z + 7 dígitos + letra) con letra de control.
 */
function fv_documento_identidad_es(string $raw): bool
{
    $doc = strtoupper(preg_replace('/\s+/', '', $raw) ?? '');
    if ($doc === '') {
        return false;
    }
    $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
    if (preg_match('/^\d{8}[A-Z]$/', $doc)) {
        $num = (int) substr($doc, 0, 8);
        $letra = $doc[8];

        return $letters[$num % 23] === $letra;
    }
    if (preg_match('/^[XYZ]\d{7}[A-Z]$/', $doc)) {
        $map = ['X' => '0', 'Y' => '1', 'Z' => '2'];
        $num = (int) ($map[$doc[0]] . substr($doc, 1, 7));
        $letra = $doc[8];

        return $letters[$num % 23] === $letra;
    }

    return false;
}

/**
 * Contraseña mínima histórica (8 caracteres). Preferir fv_clave_fuerte.
 */
function fv_clave_registro_valida(string $clave): bool
{
    return fv_clave_fuerte($clave);
}

/**
 * Política fuerte: ≥16 caracteres, mayúscula, minúscula, dígito y símbolo.
 */
function fv_clave_fuerte(string $clave): bool
{
    if (strlen($clave) < 16) {
        return false;
    }
    if (preg_match('/[A-Z]/', $clave) !== 1) {
        return false;
    }
    if (preg_match('/[a-z]/', $clave) !== 1) {
        return false;
    }
    if (preg_match('/[0-9]/', $clave) !== 1) {
        return false;
    }
    if (preg_match('/[^A-Za-z0-9]/', $clave) !== 1) {
        return false;
    }

    return true;
}

/** Identificador de login: email válido o DNI/NIE válido. */
function fv_login_identificador(string $raw): bool
{
    $t = trim($raw);
    if ($t === '') {
        return false;
    }
    if (fv_email_valido(strtolower($t))) {
        return true;
    }

    return fv_documento_identidad_es($t);
}

/**
 * Teléfono España: opcional si viene vacío; si no, 9 dígitos (móvil 6–9 o fijo habitual).
 * Acepta +34, espacios y guiones.
 */
function fv_telefono_es_opcional(string $raw): bool
{
    $t = preg_replace('/[\s.-]/', '', trim($raw));
    if ($t === '') {
        return true;
    }
    if (str_starts_with($t, '+34')) {
        $t = substr($t, 3);
    } elseif (str_starts_with($t, '0034')) {
        $t = substr($t, 4);
    }

    return preg_match('/^[6-9]\d{8}$/', $t) === 1;
}

/** Teléfono obligatorio (mismo criterio que fv_telefono_es_opcional sin vacío). */
function fv_telefono_es_obligatorio(string $raw): bool
{
    return fv_telefono_es_opcional($raw) && trim(preg_replace('/[\s.-]/', '', $raw)) !== '';
}

/**
 * Devuelve los 9 dígitos nacionales (sin prefijo) si el teléfono es válido en ES; si no, null.
 */
function fv_telefono_es_a_digitos9(string $raw): ?string
{
    $t = preg_replace('/[\s.-]/', '', trim($raw));
    if ($t === '') {
        return null;
    }
    if (str_starts_with($t, '+34')) {
        $t = substr($t, 3);
    } elseif (str_starts_with($t, '0034')) {
        $t = substr($t, 4);
    }
    if (preg_match('/^[6-9]\d{8}$/', $t) !== 1) {
        return null;
    }

    return $t;
}
