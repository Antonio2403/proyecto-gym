<?php

declare(strict_types=1);

/**
 * Borra datos de prueba y deja un admin + datos demo. El admin queda con el correo indicado y contraseña conocida.
 *
 * Uso: php scripts/seed_demo_data.php --force
 */

chdir(dirname(__DIR__));

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "Este script solo se ejecuta por línea de comandos.\n";
    exit(1);
}

if (!in_array('--force', $argv ?? [], true)) {
    fwrite(STDERR, "Este script borra datos de la base. Ejecuta: php scripts/seed_demo_data.php --force\n");
    exit(1);
}

require __DIR__ . '/../vendor/autoload.php';

Dotenv\Dotenv::createImmutable(dirname(__DIR__), ['.env', 'env'])->safeLoad();

require __DIR__ . '/../app/modelos/database.php';

const SEED_ADMIN_EMAIL = 'alfonsojaime02@gmail.com';

$pdo = BasedeDatos::Conectar();
if (!$pdo instanceof PDO) {
    fwrite(STDERR, "No se pudo conectar a la base de datos.\n");
    exit(1);
}

$st = $pdo->query(
    'SELECT u.id FROM usuarios u
     INNER JOIN administradores a ON a.usuario_id = u.id
     ORDER BY u.id ASC LIMIT 1'
);
$adminUsuarioId = $st ? (int) $st->fetchColumn() : 0;
if ($adminUsuarioId < 1) {
    fwrite(STDERR, "No hay usuario administrador (tabla administradores). Abre la web una vez para crear el esquema.\n");
    exit(1);
}

$st = $pdo->query('SELECT id FROM administradores WHERE usuario_id = ' . $adminUsuarioId . ' LIMIT 1');
$adminRowId = $st ? (int) $st->fetchColumn() : 0;
if ($adminRowId < 1) {
    fwrite(STDERR, "No hay fila en administradores para el admin.\n");
    exit(1);
}

$demoPass = 'Demo#Gym2026Seguro!';
$hash = password_hash($demoPass, PASSWORD_DEFAULT);
$adminHash = password_hash($demoPass, PASSWORD_DEFAULT);

$pdo->exec('SET FOREIGN_KEY_CHECKS=0');
$pdo->exec('DELETE FROM materiales');
$pdo->exec('DELETE FROM citas');
$pdo->exec('DELETE FROM comentarios');
$pdo->exec('DELETE FROM inscripciones');
$pdo->exec('DELETE FROM cliente_subscripcion');
$pdo->exec('DELETE FROM solicitudes');
$pdo->exec('DELETE FROM actividades');
$pdo->exec('DELETE FROM salas');
$pdo->exec('DELETE FROM monitores');
$pdo->exec('DELETE FROM clientes');
$pdo->exec('DELETE FROM fisioterapeutas');
try {
    $pdo->exec('DELETE FROM recuperacion_cuenta_ticket');
} catch (Throwable $e) {
}
$pdo->prepare('DELETE FROM usuarios WHERE id <> ?')->execute([$adminUsuarioId]);
$pdo->exec('SET FOREIGN_KEY_CHECKS=1');

$pdo->prepare(
    'UPDATE usuarios SET DNI = ?, email = ?, clave = ?, email_confirmado = 1,
     token_confirmacion = NULL, token_confirmacion_expira = NULL, password_changed_at = NOW()
     WHERE id = ?'
)->execute(['12345678Z', SEED_ADMIN_EMAIL, $adminHash, $adminUsuarioId]);

$insUser = $pdo->prepare(
    'INSERT INTO usuarios (DNI, nombre, apellido1, apellido2, email, clave, telefono, password_changed_at, email_confirmado, token_confirmacion, token_confirmacion_expira)
     VALUES (:dni, :nom, :a1, :a2, :em, :cl, :tf, NOW(), 1, NULL, NULL)'
);

$users = [
    ['10000000Z', 'Marina', 'López', '', 'monitor1@gym.demo', '600111001'],
    ['10000001S', 'Carlos', 'Ruiz', 'Díaz', 'monitor2@gym.demo', '600111002'],
    ['10000002Q', 'Ana', 'García', 'Pérez', 'cliente1@gym.demo', '600222001'],
    ['10000003V', 'Luis', 'Martín', '', 'cliente2@gym.demo', '600222002'],
    ['10000004H', 'Elena', 'Sanz', 'Torres', 'cliente3@gym.demo', '600222003'],
    ['10000005L', 'Pedro', 'Vega', '', 'fisio@gym.demo', '600333001'],
];

foreach ($users as $u) {
    $insUser->execute([
        ':dni' => $u[0],
        ':nom' => $u[1],
        ':a1' => $u[2],
        ':a2' => $u[3],
        ':em' => $u[4],
        ':cl' => $hash,
        ':tf' => $u[5],
    ]);
}

$idMonitor1User = (int) $pdo->query("SELECT id FROM usuarios WHERE email = 'monitor1@gym.demo'")->fetchColumn();
$idMonitor2User = (int) $pdo->query("SELECT id FROM usuarios WHERE email = 'monitor2@gym.demo'")->fetchColumn();
$idCliente1User = (int) $pdo->query("SELECT id FROM usuarios WHERE email = 'cliente1@gym.demo'")->fetchColumn();
$idCliente2User = (int) $pdo->query("SELECT id FROM usuarios WHERE email = 'cliente2@gym.demo'")->fetchColumn();
$idCliente3User = (int) $pdo->query("SELECT id FROM usuarios WHERE email = 'cliente3@gym.demo'")->fetchColumn();
$idFisioUser = (int) $pdo->query("SELECT id FROM usuarios WHERE email = 'fisio@gym.demo'")->fetchColumn();

$pdo->prepare('INSERT INTO monitores (usuario_id, especialidad, disponibilidad) VALUES (?, ?, ?)')->execute([$idMonitor1User, 'Spinning', 'L-V mañanas']);
$pdo->prepare('INSERT INTO monitores (usuario_id, especialidad, disponibilidad) VALUES (?, ?, ?)')->execute([$idMonitor2User, 'HIIT', 'L-V tardes']);
$idMonitor1 = (int) $pdo->query('SELECT id FROM monitores WHERE usuario_id = ' . $idMonitor1User)->fetchColumn();
$idMonitor2 = (int) $pdo->query('SELECT id FROM monitores WHERE usuario_id = ' . $idMonitor2User)->fetchColumn();

$pdo->prepare('INSERT INTO clientes (usuario_id, metodo_pago) VALUES (?, ?)')->execute([$idCliente1User, 'tarjeta']);
$pdo->prepare('INSERT INTO clientes (usuario_id, metodo_pago) VALUES (?, ?)')->execute([$idCliente2User, 'bizum']);
$pdo->prepare('INSERT INTO clientes (usuario_id, metodo_pago) VALUES (?, ?)')->execute([$idCliente3User, 'tarjeta']);
$idCliente1 = (int) $pdo->query('SELECT id FROM clientes WHERE usuario_id = ' . $idCliente1User)->fetchColumn();
$idCliente2 = (int) $pdo->query('SELECT id FROM clientes WHERE usuario_id = ' . $idCliente2User)->fetchColumn();
$idCliente3 = (int) $pdo->query('SELECT id FROM clientes WHERE usuario_id = ' . $idCliente3User)->fetchColumn();

$pdo->prepare('INSERT INTO fisioterapeutas (nombre, especialidad, usuario_id) VALUES (?, ?, ?)')->execute(['Pedro Vega', 'Columna y deporte', $idFisioUser]);
$idFisio = (int) $pdo->query('SELECT id FROM fisioterapeutas WHERE usuario_id = ' . $idFisioUser)->fetchColumn();

$pdo->exec("INSERT INTO salas (nombre, capacidad, disponibilidad) VALUES ('Sala multiusos', 20, 'L')");
$pdo->exec("INSERT INTO salas (nombre, capacidad, disponibilidad) VALUES ('Sala spinning', 15, 'U')");
$pdo->exec("INSERT INTO salas (nombre, capacidad, disponibilidad) VALUES ('Sala musculación', 25, 'R')");
$idSala1 = (int) $pdo->query("SELECT id FROM salas WHERE nombre = 'Sala multiusos'")->fetchColumn();
$idSala2 = (int) $pdo->query("SELECT id FROM salas WHERE nombre = 'Sala spinning'")->fetchColumn();
$idSala3 = (int) $pdo->query("SELECT id FROM salas WHERE nombre = 'Sala musculación'")->fetchColumn();

$insAct = $pdo->prepare(
    'INSERT INTO actividades (nombre, duracion, monitor_id, sala_id, fecha_inicio, fecha_fin, dia_semana, descripcion, plazas, recurrente)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$insAct->execute([
    'Spinning matinal',
    45,
    $idMonitor1,
    $idSala2,
    '2026-05-12 08:00:00',
    '2026-05-12 08:45:00',
    'L',
    'Sesión cardio en bicicleta.',
    15,
    1,
]);
$insAct->execute([
    'HIIT express',
    30,
    $idMonitor2,
    $idSala1,
    '2026-05-13 18:30:00',
    '2026-05-13 19:00:00',
    'X',
    'Intervalos de alta intensidad.',
    12,
    1,
]);
$insAct->execute([
    'Yoga suave',
    60,
    $idMonitor1,
    $idSala1,
    '2026-05-14 10:00:00',
    '2026-05-14 11:00:00',
    'J',
    'Estiramientos y respiración.',
    20,
    1,
]);
$idAct1 = (int) $pdo->query("SELECT id FROM actividades WHERE nombre = 'Spinning matinal'")->fetchColumn();
$idAct2 = (int) $pdo->query("SELECT id FROM actividades WHERE nombre = 'HIIT express'")->fetchColumn();
$idAct3 = (int) $pdo->query("SELECT id FROM actividades WHERE nombre = 'Yoga suave'")->fetchColumn();

$pdo->prepare(
    'INSERT INTO solicitudes (monitor_id, admin_id, tipo, descripcion, estado, fecha_creacion, fecha_revision)
     VALUES (?, ?, ?, ?, ?, NOW(), NULL)'
)->execute([$idMonitor1, $adminRowId, 'Cambio horario', 'Solicito cambiar el martes por el miércoles.', 'P']);
$pdo->prepare(
    'INSERT INTO solicitudes (monitor_id, admin_id, tipo, descripcion, estado, fecha_creacion, fecha_revision)
     VALUES (?, ?, ?, ?, ?, NOW(), ?)'
)->execute([$idMonitor2, $adminRowId, 'Material', 'Falta agua en la sala 2.', 'A', '2026-05-01 12:00:00']);
$pdo->prepare(
    'INSERT INTO solicitudes (monitor_id, admin_id, tipo, descripcion, estado, fecha_creacion, fecha_revision)
     VALUES (?, ?, ?, ?, ?, NOW(), ?)'
)->execute([$idMonitor1, $adminRowId, 'Vacaciones', 'Semana del 15 al 22 de agosto.', 'R', '2026-05-02 09:00:00']);

$subIds = $pdo->query('SELECT id, nombre FROM subscripciones ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
$idSubNormal = null;
$idSubPro = null;
$idSubMega = null;
foreach ($subIds as $row) {
    $n = (string) $row['nombre'];
    if ($n === 'Normal') {
        $idSubNormal = (int) $row['id'];
    } elseif ($n === 'Pro') {
        $idSubPro = (int) $row['id'];
    } elseif ($n === 'MegaPro') {
        $idSubMega = (int) $row['id'];
    }
}
if ($idSubNormal === null || $idSubPro === null || $idSubMega === null) {
    fwrite(STDERR, "Faltan filas Normal/Pro/MegaPro en subscripciones.\n");
    exit(1);
}

$pdo->prepare(
    'INSERT INTO cliente_subscripcion (cliente_id, subscripcion_id, fecha_inicio, fecha_fin, estado)
     VALUES (?, ?, ?, ?, ?)'
)->execute([$idCliente1, $idSubNormal, '2026-05-01 00:00:00', '2026-06-01 23:59:59', 'A']);
$pdo->prepare(
    'INSERT INTO cliente_subscripcion (cliente_id, subscripcion_id, fecha_inicio, fecha_fin, estado)
     VALUES (?, ?, ?, ?, ?)'
)->execute([$idCliente2, $idSubPro, '2026-05-01 00:00:00', '2026-07-01 23:59:59', 'A']);
$pdo->prepare(
    'INSERT INTO cliente_subscripcion (cliente_id, subscripcion_id, fecha_inicio, fecha_fin, estado)
     VALUES (?, ?, ?, ?, ?)'
)->execute([$idCliente3, $idSubMega, '2026-05-01 00:00:00', '2026-08-01 23:59:59', 'A']);

$pdo->prepare(
    'INSERT INTO inscripciones (cliente_id, actividad_id, fecha_ocurrencia, asistio, fecha_inscripcion)
     VALUES (?, ?, ?, ?, NOW())'
)->execute([$idCliente1, $idAct1, '2026-05-12', 'S']);
$pdo->prepare(
    'INSERT INTO inscripciones (cliente_id, actividad_id, fecha_ocurrencia, asistio, fecha_inscripcion)
     VALUES (?, ?, ?, ?, NOW())'
)->execute([$idCliente2, $idAct2, '2026-05-13', 'N']);
$pdo->prepare(
    'INSERT INTO inscripciones (cliente_id, actividad_id, fecha_ocurrencia, asistio, fecha_inscripcion)
     VALUES (?, ?, ?, ?, NOW())'
)->execute([$idCliente3, $idAct3, '2026-05-14', 'S']);

$pdo->prepare(
    'INSERT INTO comentarios (cliente_id, actividad_id, fecha_ocurrencia, texto, fecha)
     VALUES (?, ?, ?, ?, NOW())'
)->execute([$idCliente1, $idAct1, '2026-05-12', 'Muy buena energía, repetiré.']);
$pdo->prepare(
    'INSERT INTO comentarios (cliente_id, actividad_id, fecha_ocurrencia, texto, fecha)
     VALUES (?, ?, ?, ?, NOW())'
)->execute([$idCliente3, $idAct3, '2026-05-14', 'Ideal para desconectar.']);

$pdo->exec(
    "INSERT INTO feedback (nombre, email, asunto, mensaje, fecha_creacion) VALUES
     ('Visitante', 'visitante@demo.local', 'Horario', '¿Abrís fines de semana por la tarde?', NOW()),
     ('Socio', 'socio@demo.local', 'Felicitación', 'El equipo del gimnasio es muy amable.', NOW())"
);

$pdo->prepare(
    'INSERT INTO citas (cliente_id, fisio_id, fecha, motivo, estado)
     VALUES (?, ?, ?, ?, ?)'
)->execute([$idCliente3, $idFisio, '2026-05-20 11:00:00', 'Dolor lumbar tras entreno', 'S']);
$pdo->prepare(
    'INSERT INTO citas (cliente_id, fisio_id, fecha, motivo, estado)
     VALUES (?, ?, ?, ?, ?)'
)->execute([$idCliente3, $idFisio, '2026-05-22 16:30:00', 'Revisión', 'C']);

$pdo->prepare('INSERT INTO materiales (sala_id, nombre, estado) VALUES (?, ?, ?)')->execute([$idSala3, 'Banco regulable', 'B']);
$pdo->prepare('INSERT INTO materiales (sala_id, nombre, estado) VALUES (?, ?, ?)')->execute([$idSala3, 'Mancuernas 2–20 kg', 'B']);
$pdo->prepare('INSERT INTO materiales (sala_id, nombre, estado) VALUES (?, ?, ?)')->execute([$idSala2, 'Bicicleta #3', 'M']);

echo "Datos de demo insertados.\n\n";
echo "Contraseña de todos los usuarios de prueba (incl. administrador): {$demoPass}\n\n";
echo 'Administrador: ' . SEED_ADMIN_EMAIL . " / {$demoPass}\n";
echo "Monitores: monitor1@gym.demo, monitor2@gym.demo\n";
echo "Clientes: cliente1@gym.demo, cliente2@gym.demo, cliente3@gym.demo (MegaPro + fisio)\n";
echo "Fisioterapeuta (usuario): fisio@gym.demo\n";
