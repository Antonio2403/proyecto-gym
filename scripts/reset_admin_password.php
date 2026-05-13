<?php

declare(strict_types=1);

/**
 * Restablece la contraseña del administrador cuando no la recuerdas (acceso físico / servidor).
 *
 * Uso:
 *   php scripts/reset_admin_password.php 'TuNuevaClaveMuySegura!123'
 *   php scripts/reset_admin_password.php --email admin@gym.com 'TuNuevaClaveMuySegura!123'
 *
 * Requisitos de la nueva clave: iguales que en la web (≥16 caracteres, mayúsculas, minúsculas, número, símbolo).
 */

chdir(dirname(__DIR__));

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Este script solo se ejecuta por línea de comandos.\n");
    exit(1);
}

require __DIR__ . '/../vendor/autoload.php';

Dotenv\Dotenv::createImmutable(dirname(__DIR__), ['.env', 'env'])->safeLoad();

require_once __DIR__ . '/../app/modelos/database.php';
require_once __DIR__ . '/../core/helpers/form_validacion.php';
require_once __DIR__ . '/../app/modelos/usuario.php';

$args = array_slice($argv, 1);
$email = null;

for ($i = 0; $i < count($args); $i++) {
    if ($args[$i] === '--email' && isset($args[$i + 1])) {
        $email = strtolower(trim($args[$i + 1]));
        array_splice($args, $i, 2);
        break;
    }
}

if (count($args) !== 1) {
    fwrite(STDERR, "Uso:\n");
    fwrite(STDERR, "  php scripts/reset_admin_password.php [--email correo@del.admin] 'NuevaContraseña'\n\n");
    fwrite(STDERR, "Sin --email se usa la primera cuenta que exista en la tabla administradores.\n");
    exit(1);
}

$newPlain = $args[0];

$pdo = BasedeDatos::Conectar();
if (!$pdo instanceof PDO) {
    fwrite(STDERR, "No se pudo conectar a la base de datos. Revisa DB_* en env o .env.\n");
    exit(1);
}

if ($email !== null && $email !== '') {
    $st = $pdo->prepare(
        'SELECT u.id, u.email FROM usuarios u
         INNER JOIN administradores a ON a.usuario_id = u.id
         WHERE LOWER(TRIM(u.email)) = ?
         LIMIT 1'
    );
    $st->execute([$email]);
} else {
    $st = $pdo->query(
        'SELECT u.id, u.email FROM usuarios u
         INNER JOIN administradores a ON a.usuario_id = u.id
         ORDER BY u.id ASC
         LIMIT 1'
    );
}

$row = $st ? $st->fetch(PDO::FETCH_ASSOC) : false;
if ($row === false) {
    fwrite(STDERR, "No se encontró ningún administrador" . ($email !== null ? " con el email indicado.\n" : ".\n"));
    exit(1);
}

$id = (int) $row['id'];
$foundEmail = (string) $row['email'];

if (!Usuario::establecerClaveFuerte($id, $newPlain)) {
    fwrite(STDERR, "No se pudo guardar. Comprueba que la contraseña cumple la política de complejidad.\n");
    exit(1);
}

echo "Contraseña actualizada para el administrador: {$foundEmail} (usuario id {$id}).\n";
echo "Ya puedes iniciar sesión en /login con ese correo o DNI y la nueva contraseña.\n";
