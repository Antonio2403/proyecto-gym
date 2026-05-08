<?php

declare(strict_types=1);

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
 * Contraseña mínima razonable para registro / alta de usuarios.
 */
function fv_clave_registro_valida(string $clave): bool
{
    return strlen($clave) >= 8;
}
