<?php

declare(strict_types=1);

/**
 * Restablece la base de datos importando scripts/spartum_full_demo.sql
 * (esquema completo + datos de demostración).
 *
 * Uso:
 *   php scripts/import_demo_database.php --force
 *
 * Requiere cliente mysql en PATH o en /opt/lampp/bin/mysql (XAMPP).
 * Lee credenciales de .env / env (DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_SOCKET).
 */

chdir(dirname(__DIR__));

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "Este script solo se ejecuta por línea de comandos.\n";
    exit(1);
}

if (!in_array('--force', $argv ?? [], true)) {
    fwrite(STDERR, "Este script borra y recrea todas las tablas de la BD configurada.\n");
    fwrite(STDERR, "Ejecuta: php scripts/import_demo_database.php --force\n");
    exit(1);
}

require __DIR__ . '/../vendor/autoload.php';

Dotenv\Dotenv::createImmutable(dirname(__DIR__), ['.env', 'env'])->safeLoad();

function envStr(string $key, string $default = ''): string
{
    $v = $_ENV[$key] ?? getenv($key);
    if ($v === false || $v === null) {
        return $default;
    }

    return (string) $v;
}

$dbName = envStr('DB_DATABASE', 'spartum');
$dbUser = envStr('DB_USERNAME', 'root');
$dbPass = envStr('DB_PASSWORD', '');
$dbHost = envStr('DB_HOST', '127.0.0.1');
$dbPort = envStr('DB_PORT', '3306');
$dbSocket = envStr('DB_SOCKET', '');

if ($dbHost === 'localhost') {
    $dbHost = '127.0.0.1';
}

$sqlFile = __DIR__ . '/spartum_full_demo.sql';
if (!is_readable($sqlFile)) {
    fwrite(STDERR, "No se encuentra el archivo SQL: {$sqlFile}\n");
    exit(1);
}

function findMysqlClient(): ?string
{
    $custom = envStr('MYSQL_CLI', '');
    if ($custom !== '' && is_executable($custom)) {
        return $custom;
    }
    $candidates = ['/opt/lampp/bin/mysql', '/usr/bin/mysql', '/usr/local/bin/mysql'];
    foreach ($candidates as $bin) {
        if (is_executable($bin)) {
            return $bin;
        }
    }
    $which = trim((string) shell_exec('command -v mysql 2>/dev/null'));
    if ($which !== '' && is_executable($which)) {
        return $which;
    }

    return null;
}

function pdoDsn(bool $withDb): string
{
    global $dbSocket, $dbHost, $dbPort, $dbName;
    $charset = 'utf8';
    if ($dbSocket !== '') {
        return $withDb
            ? 'mysql:unix_socket=' . $dbSocket . ';dbname=' . $dbName . ';charset=' . $charset
            : 'mysql:unix_socket=' . $dbSocket . ';charset=' . $charset;
    }

    return $withDb
        ? 'mysql:host=' . $dbHost . ';port=' . $dbPort . ';dbname=' . $dbName . ';charset=' . $charset
        : 'mysql:host=' . $dbHost . ';port=' . $dbPort . ';charset=' . $charset;
}

try {
    $pdoServer = new PDO(pdoDsn(false), $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdoServer->exec(
        'CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', $dbName) . '` CHARACTER SET utf8 COLLATE utf8_general_ci'
    );
} catch (Throwable $e) {
    fwrite(STDERR, 'No se pudo conectar o crear la base de datos: ' . $e->getMessage() . "\n");
    exit(1);
}

$mysql = findMysqlClient();
if ($mysql === null) {
    fwrite(STDERR, "No se encontró el cliente mysql. Instálalo o define MYSQL_CLI en .env\n");
    exit(1);
}

$cmdParts = [escapeshellarg($mysql)];
if ($dbSocket !== '') {
    $cmdParts[] = '--socket=' . escapeshellarg($dbSocket);
} else {
    $cmdParts[] = '-h' . escapeshellarg($dbHost);
    $cmdParts[] = '-P' . escapeshellarg($dbPort);
}
$cmdParts[] = '-u' . escapeshellarg($dbUser);
if ($dbPass !== '') {
    $cmdParts[] = '-p' . escapeshellarg($dbPass);
}
$cmdParts[] = escapeshellarg($dbName);
$cmdParts[] = '<';
$cmdParts[] = escapeshellarg($sqlFile);

$fullCmd = implode(' ', $cmdParts) . ' 2>&1';
exec($fullCmd, $outputLines, $exitCode);

if ($exitCode !== 0) {
    fwrite(STDERR, "Error al importar SQL (código {$exitCode}).\n");
    if ($outputLines !== []) {
        fwrite(STDERR, implode("\n", $outputLines) . "\n");
    }
    exit(1);
}

$demoPass = 'Demo#Gym2026Seguro!';
$adminEmail = 'alfonsojaime02@gmail.com';
$credFile = dirname(__DIR__) . '/CREDENCIALES_DEMO.txt';
$credBody = <<<TXT
SPARTUM — Credenciales de prueba (demo)
========================================
Generado: {date}
Base de datos: {$dbName}
Contraseña común para TODAS las cuentas: {$demoPass}

URL login: http://localhost/proyecto-gym/login

ADMIN:     {$adminEmail} / {$demoPass}
MONITOR 1: monitor1@gym.demo / {$demoPass}
MONITOR 2: monitor2@gym.demo / {$demoPass}
CLIENTE 1: cliente1@gym.demo / {$demoPass}
CLIENTE 2: cliente2@gym.demo / {$demoPass}
CLIENTE 3: cliente3@gym.demo / {$demoPass}
FISIO:     fisio@gym.demo / {$demoPass}

Recargar BD demo: php scripts/import_demo_database.php --force
TXT;
$credBody = str_replace('{date}', date('Y-m-d H:i:s'), $credBody);
file_put_contents($credFile, $credBody);

echo "Base de datos `{$dbName}` importada desde spartum_full_demo.sql\n";
echo "Contraseña de todas las cuentas demo: {$demoPass}\n";
echo "Administrador: {$adminEmail}\n";
echo "Credenciales guardadas en: {$credFile}\n";
