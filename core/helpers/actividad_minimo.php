<?php

declare(strict_types=1);

require_once __DIR__ . '/actividad_notificaciones.php';
require_once dirname(__DIR__, 2) . '/app/modelos/database.php';
require_once dirname(__DIR__, 2) . '/app/modelos/actividades.php';
require_once dirname(__DIR__, 2) . '/app/modelos/inscripcion.php';

const GP_CLASE_MINIMO_PERSONAS = 5;
const GP_CLASE_MINIMO_ANTELACION_SEG = 30 * 60;

/**
 * Media hora antes de cada clase: si hay menos de 5 personas, cancela esa sesión
 * y manda el aviso a quien estaba apuntado y el reporte al monitor y a administración.
 *
 * @return list<array{nombre: string, fecha: string, hora: string, inscritos: int}>
 */
function gp_actividad_revisar_minimo_asistencia(): array
{
    $ahora = time();
    $tz = new DateTimeZone(date_default_timezone_get());
    $hoy = (new DateTimeImmutable('today', $tz));
    $fechas = [$hoy->format('Y-m-d'), $hoy->modify('+1 day')->format('Y-m-d')];
    $canceladas = [];

    foreach (Actividad::obtenerTodas() as $act) {
        $id = (int) ($act['id'] ?? 0);
        if ($id <= 0) {
            continue;
        }
        foreach ($fechas as $fecha) {
            if (!Actividad::fechaEsSesionValida($id, $fecha)) {
                continue;
            }
            $inicio = Actividad::inicioSesionTimestamp($act, $fecha);
            if ($inicio === null) {
                continue;
            }
            if ($ahora < ($inicio - GP_CLASE_MINIMO_ANTELACION_SEG) || $ahora >= $inicio) {
                continue;
            }
            if (Actividad::sesionCanceladaPorMinimo($id, $fecha)) {
                continue;
            }
            $cuantos = Inscripcion::contarInscritosSesion($id, $fecha);
            if ($cuantos >= GP_CLASE_MINIMO_PERSONAS) {
                continue;
            }
            $hora = date('H:i', $inicio);
            if (gp_actividad_cancelar_por_minimo($act, $fecha, $hora, $cuantos)) {
                $canceladas[] = [
                    'nombre' => (string) ($act['nombre'] ?? 'Clase'),
                    'fecha' => $fecha,
                    'hora' => $hora,
                    'inscritos' => $cuantos,
                ];
            }
        }
    }

    return $canceladas;
}

/**
 * @param array<string, mixed> $act
 */
function gp_actividad_cancelar_por_minimo(array $act, string $fecha, string $hora, int $cuantos): bool
{
    $id = (int) ($act['id'] ?? 0);
    $db = BasedeDatos::Conectar();
    $personas = gp_actividad_personas_sesion($id, $fecha);
    $staff = gp_actividad_destinatarios_reporte($id);

    $db->beginTransaction();
    try {
        $ins = $db->prepare(
            'INSERT IGNORE INTO actividad_sesion_cancelada (actividad_id, fecha, inscritos, minimo, cancelada_en)
             VALUES (?, ?, ?, ?, NOW())'
        );
        $ins->execute([$id, $fecha, $cuantos, GP_CLASE_MINIMO_PERSONAS]);
        if ($ins->rowCount() === 0) {
            $db->rollBack();

            return false;
        }
        $del = $db->prepare('DELETE FROM inscripciones WHERE actividad_id = ? AND fecha_ocurrencia = ?');
        $del->execute([$id, $fecha]);
        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log('[Spartum actividad] no se pudo cancelar por mínimo: ' . $e->getMessage());

        return false;
    }

    $nombre = (string) ($act['nombre'] ?? 'Clase');
    gp_actividad_notificar_minimo_no_alcanzado(
        $nombre,
        $fecha,
        $hora,
        $cuantos,
        GP_CLASE_MINIMO_PERSONAS,
        $personas
    );
    gp_actividad_reportar_minimo_no_alcanzado(
        $nombre,
        $fecha,
        $hora,
        $cuantos,
        GP_CLASE_MINIMO_PERSONAS,
        $staff,
        array_map(static fn (array $p): string => (string) ($p['nombre'] ?? ''), $personas)
    );

    return true;
}

/**
 * @return list<array{email: string, nombre: string}>
 */
function gp_actividad_personas_sesion(int $actividadId, string $fecha): array
{
    $db = BasedeDatos::Conectar();
    $st = $db->prepare(
        'SELECT u.email, u.nombre
         FROM inscripciones i
         INNER JOIN clientes c ON c.id = i.cliente_id
         INNER JOIN usuarios u ON u.id = c.usuario_id
         WHERE i.actividad_id = ? AND i.fecha_ocurrencia = ?'
    );
    $st->execute([$actividadId, $fecha]);
    $out = [];
    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $out[] = [
            'email' => (string) ($row['email'] ?? ''),
            'nombre' => (string) ($row['nombre'] ?? ''),
        ];
    }

    return $out;
}

/**
 * @return list<array{email: string, nombre: string}>
 */
function gp_actividad_destinatarios_reporte(int $actividadId): array
{
    $db = BasedeDatos::Conectar();
    $st = $db->prepare(
        'SELECT u.email, u.nombre
         FROM actividades a
         INNER JOIN monitores m ON m.id = a.monitor_id
         INNER JOIN usuarios u ON u.id = m.usuario_id
         WHERE a.id = ?
         UNION
         SELECT u.email, u.nombre
         FROM administradores ad
         INNER JOIN usuarios u ON u.id = ad.usuario_id'
    );
    $st->execute([$actividadId]);
    $vistos = [];
    $out = [];
    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $email = strtolower(trim((string) ($row['email'] ?? '')));
        if ($email === '' || isset($vistos[$email])) {
            continue;
        }
        $vistos[$email] = true;
        $out[] = [
            'email' => (string) $row['email'],
            'nombre' => (string) ($row['nombre'] ?? ''),
        ];
    }

    return $out;
}
