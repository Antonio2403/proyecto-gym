<?php

declare(strict_types=1);

/**
 * Base URL path for the app (no trailing slash), e.g. "/proyecto-gym" or "" at domain root.
 */
function app_base_path(): string
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $fromEnv = $_ENV['APP_BASE_PATH'] ?? getenv('APP_BASE_PATH');
    if (is_string($fromEnv) && $fromEnv !== '') {
        return $cached = '/' . trim($fromEnv, '/');
    }

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $dir = str_replace('\\', '/', dirname($scriptName));

    if ($dir === '/' || $dir === '.') {
        return $cached = '';
    }

    return $cached = rtrim($dir, '/');
}

/**
 * Absolute path from web root (includes subdirectory base when applicable).
 */
function url(string $path = '/'): string
{
    $base = app_base_path();
    $path = '/' . ltrim($path, '/');

    if ($path === '/') {
        return $base === '' ? '/' : $base;
    }

    return ($base === '' ? '' : $base) . $path;
}

function asset(string $path): string
{
    return url('/public/assets/' . ltrim($path, '/'));
}

/**
 * Prefijo que Bramus Router resta de REQUEST_URI (ver Router::getCurrentUri).
 * Debe estar alineado con app_base_path() para que las URLs generadas con url() coincidan con el enrutado.
 */
function router_server_base_path(): string
{
    $base = app_base_path();
    if ($base === '' || $base === '/') {
        return '/';
    }

    return rtrim($base, '/') . '/';
}

/**
 * URL absoluta (esquema + host + path de la app). Si APP_URL incluye la subcarpeta
 * de la aplicación, no se duplica app_base_path().
 */
function app_url_absolute(string $path = '/'): string
{
    $fromEnv = $_ENV['APP_URL'] ?? getenv('APP_URL');
    if (is_string($fromEnv) && trim($fromEnv) !== '') {
        return rtrim($fromEnv, '/') . '/' . ltrim($path, '/');
    }

    $rel = url($path);
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    return $scheme . '://' . $host . ($rel === '' ? '/' : $rel);
}
