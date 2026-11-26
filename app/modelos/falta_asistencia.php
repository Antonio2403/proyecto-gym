<?php

declare(strict_types=1);

require_once __DIR__ . '/database.php';

/**
 * Faltas que marca el monitor de la clase. Cada falta queda en su propio registro.
 */
class FaltaAsistencia
{
    /**
     * @return array{ok: bool, error?: string}
     */
    public static function poner(int $inscripcionId, int $monitorId): array
    {
        $sesion = self::sesionDelMonitor($inscripcionId, $monitorId);
        if ($sesion === null) {
            return ['ok' => false, 'error' => 'Esa inscripción no es de una clase tuya.'];
        }
        if (!self::sesionYaRealizada($sesion)) {
            return ['ok' => false, 'error' => 'No puedes poner falta en una clase que aún no se ha realizado.'];
        }
        $db = BasedeDatos::Conectar();
        $ya = $db->prepare(
            'SELECT id FROM falta_asistencia
             WHERE cliente_id = ? AND actividad_id = ? AND fecha = ?
             LIMIT 1'
        );
        $ya->execute([$sesion['cliente_id'], $sesion['actividad_id'], $sesion['fecha']]);
        if ($ya->fetchColumn()) {
            return ['ok' => false, 'error' => 'Esa falta ya está registrada.'];
        }
        $ins = $db->prepare(
            'INSERT INTO falta_asistencia (cliente_id, actividad_id, fecha, monitor_id, creada_en)
             VALUES (?, ?, ?, ?, ?)'
        );
        $ins->execute([
            $sesion['cliente_id'],
            $sesion['actividad_id'],
            $sesion['fecha'],
            $monitorId,
            (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
        ]);
        $marca = $db->prepare("UPDATE inscripciones SET asistio = 'N' WHERE id = ?");
        $marca->execute([$inscripcionId]);

        return ['ok' => true];
    }

    /**
     * @return array{ok: bool, error?: string}
     */
    public static function quitar(int $inscripcionId, int $monitorId): array
    {
        $sesion = self::sesionDelMonitor($inscripcionId, $monitorId);
        if ($sesion === null) {
            return ['ok' => false, 'error' => 'Esa inscripción no es de una clase tuya.'];
        }
        $db = BasedeDatos::Conectar();
        $del = $db->prepare(
            'DELETE FROM falta_asistencia
             WHERE cliente_id = ? AND actividad_id = ? AND fecha = ?'
        );
        $del->execute([$sesion['cliente_id'], $sesion['actividad_id'], $sesion['fecha']]);
        if ($del->rowCount() < 1) {
            return ['ok' => false, 'error' => 'Esa persona no tenía falta en esa clase.'];
        }
        $marca = $db->prepare("UPDATE inscripciones SET asistio = 'S' WHERE id = ?");
        $marca->execute([$inscripcionId]);

        return ['ok' => true];
    }

    /**
     * @param list<int> $inscripcionIds
     * @return array<int, true>
     */
    public static function marcadasPorInscripcion(array $inscripcionIds): array
    {
        $ids = [];
        foreach ($inscripcionIds as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $ids[$id] = $id;
            }
        }
        if ($ids === []) {
            return [];
        }
        $db = BasedeDatos::Conectar();
        $marcas = implode(',', array_fill(0, count($ids), '?'));
        $st = $db->prepare(
            "SELECT i.id
             FROM inscripciones i
             INNER JOIN falta_asistencia f
                ON f.cliente_id = i.cliente_id
               AND f.actividad_id = i.actividad_id
               AND f.fecha = i.fecha_ocurrencia
             WHERE i.id IN ($marcas)"
        );
        $st->execute(array_values($ids));
        $out = [];
        foreach ($st->fetchAll(PDO::FETCH_COLUMN) as $id) {
            $out[(int) $id] = true;
        }

        return $out;
    }

    public static function contarDeUsuario(int $usuarioId): int
    {
        if ($usuarioId <= 0) {
            return 0;
        }
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'SELECT COUNT(*)
             FROM falta_asistencia f
             INNER JOIN clientes c ON c.id = f.cliente_id
             WHERE c.usuario_id = ?'
        );
        $st->execute([$usuarioId]);

        return (int) $st->fetchColumn();
    }

    /**
     * @return list<array{fecha: string, actividad: string, hora: string, monitor: string, creada_en: string}>
     */
    public static function deUsuario(int $usuarioId): array
    {
        if ($usuarioId <= 0) {
            return [];
        }
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'SELECT f.fecha, f.creada_en, a.nombre AS actividad, a.fecha_inicio,
                    TRIM(CONCAT(u.nombre, \' \', u.apellido1)) AS monitor
             FROM falta_asistencia f
             INNER JOIN clientes c ON c.id = f.cliente_id
             INNER JOIN actividades a ON a.id = f.actividad_id
             INNER JOIN monitores m ON m.id = f.monitor_id
             INNER JOIN usuarios u ON u.id = m.usuario_id
             WHERE c.usuario_id = ?
             ORDER BY f.fecha DESC, f.id DESC'
        );
        $st->execute([$usuarioId]);
        $filas = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $inicio = (string) ($row['fecha_inicio'] ?? '');
            $filas[] = [
                'fecha' => (string) ($row['fecha'] ?? ''),
                'actividad' => (string) ($row['actividad'] ?? ''),
                'hora' => $inicio !== '' ? date('H:i', strtotime($inicio)) : '',
                'monitor' => trim((string) ($row['monitor'] ?? '')),
                'creada_en' => (string) ($row['creada_en'] ?? ''),
            ];
        }

        return $filas;
    }

    /**
     * @return array{cliente_id: int, actividad_id: int, fecha: string, fecha_inicio: string, duracion: int}|null
     */
    private static function sesionDelMonitor(int $inscripcionId, int $monitorId): ?array
    {
        if ($inscripcionId <= 0 || $monitorId <= 0) {
            return null;
        }
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'SELECT i.cliente_id, i.actividad_id, i.fecha_ocurrencia, a.fecha_inicio, a.duracion
             FROM inscripciones i
             INNER JOIN actividades a ON a.id = i.actividad_id
             WHERE i.id = ? AND a.monitor_id = ?
             LIMIT 1'
        );
        $st->execute([$inscripcionId, $monitorId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!is_array($row) || $row['fecha_ocurrencia'] === null) {
            return null;
        }

        return [
            'cliente_id' => (int) $row['cliente_id'],
            'actividad_id' => (int) $row['actividad_id'],
            'fecha' => (string) $row['fecha_ocurrencia'],
            'fecha_inicio' => (string) ($row['fecha_inicio'] ?? ''),
            'duracion' => (int) ($row['duracion'] ?? 0),
        ];
    }

    /**
     * @param array{fecha: string, fecha_inicio: string, duracion: int} $sesion
     */
    private static function sesionYaRealizada(array $sesion): bool
    {
        require_once __DIR__ . '/actividades.php';

        return Actividad::sesionHaFinalizado(
            [
                'fecha_inicio' => $sesion['fecha_inicio'],
                'duracion' => $sesion['duracion'],
            ],
            $sesion['fecha']
        );
    }
}
