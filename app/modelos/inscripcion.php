<?php

require_once __DIR__ . '/actividades.php';

class Inscripcion
{
    public static function yaInscrito($cliente_id, $actividad_id)
    {
        return self::yaInscritoEnSesion($cliente_id, $actividad_id, null);
    }

    /** Si $fechaYmd es null, cualquier inscripción a esa actividad cuenta (compatibilidad). */
    public static function yaInscritoEnSesion($cliente_id, $actividad_id, ?string $fechaYmd): bool
    {
        $conexion = BasedeDatos::Conectar();
        if ($fechaYmd !== null && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaYmd)) {
            $stmt = $conexion->prepare(
                'SELECT COUNT(*) FROM inscripciones
                 WHERE cliente_id = :cliente_id AND actividad_id = :actividad_id AND fecha_ocurrencia = :f'
            );
            $stmt->bindValue(':cliente_id', $cliente_id);
            $stmt->bindValue(':actividad_id', $actividad_id);
            $stmt->bindValue(':f', $fechaYmd);
            $stmt->execute();

            return (int) $stmt->fetchColumn() > 0;
        }

        $stmt = $conexion->prepare(
            'SELECT COUNT(*) FROM inscripciones WHERE cliente_id = :cliente_id AND actividad_id = :actividad_id'
        );
        $stmt->bindValue(':cliente_id', $cliente_id);
        $stmt->bindValue(':actividad_id', $actividad_id);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    public static function contarInscritos($actividad_id)
    {
        return self::contarInscritosSesion($actividad_id, null);
    }

    /** Cupo por sesión concreta (fecha Y-m-d). Si fecha null, todas las inscripciones a la actividad (legacy). */
    public static function contarInscritosSesion(int $actividad_id, ?string $fechaYmd): int
    {
        $conexion = BasedeDatos::Conectar();
        if ($fechaYmd !== null && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaYmd)) {
            $stmt = $conexion->prepare(
                'SELECT COUNT(*) FROM inscripciones WHERE actividad_id = ? AND fecha_ocurrencia = ?'
            );
            $stmt->execute([$actividad_id, $fechaYmd]);

            return (int) $stmt->fetchColumn();
        }
        $stmt = $conexion->prepare('SELECT COUNT(*) FROM inscripciones WHERE actividad_id = ?');
        $stmt->execute([$actividad_id]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Fecha concreta de la sesión (Y-m-d): próximo día de la semana de la actividad, o fecha única si no es recurrente.
     * Usado al crear la inscripción y para enlaces a la página de comentarios de esa sesión.
     */
    private static function fechaOcurrenciaParaActividad(int $actividad_id): ?string
    {
        $act = Actividad::obtenerPorId($actividad_id);
        if (!$act) {
            return null;
        }

        $recurrente = (int) ($act['recurrente'] ?? 1);
        if ($recurrente === 0 && !empty($act['fecha_inicio'])) {
            return substr((string) $act['fecha_inicio'], 0, 10);
        }

        $dias = Actividad::diasParaActividadId((int) $actividad_id);
        if ($dias === []) {
            return null;
        }

        $map = ['L' => 1, 'M' => 2, 'X' => 3, 'J' => 4, 'V' => 5, 'S' => 6, 'D' => 7];
        $tz = new DateTimeZone(date_default_timezone_get());
        $cursor = new DateTime('today', $tz);
        $current = (int) $cursor->format('N');
        $bestDelta = 8;
        foreach ($dias as $dia) {
            if (!isset($map[$dia])) {
                continue;
            }
            $target = $map[$dia];
            $delta = ($target - $current + 7) % 7;
            if ($delta < $bestDelta) {
                $bestDelta = $delta;
            }
        }
        if ($bestDelta > 7) {
            return null;
        }
        $cursor->modify("+{$bestDelta} days");

        return $cursor->format('Y-m-d');
    }

    public static function fechaProximaOcurrenciaActividad(int $actividad_id): ?string
    {
        return self::fechaOcurrenciaParaActividad($actividad_id);
    }

    public static function inscribir($cliente_id, $actividad_id, ?string $fechaOcForzada = null)
    {
        $conexion = BasedeDatos::Conectar();

        $fechaOc = $fechaOcForzada;
        if ($fechaOc === null || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaOc)) {
            $fechaOc = self::fechaOcurrenciaParaActividad((int) $actividad_id);
        }

        $stmt = $conexion->prepare("
                INSERT INTO inscripciones (cliente_id, actividad_id, fecha_ocurrencia)
                VALUES (:cliente_id, :actividad_id, :fecha_ocurrencia)
            ");

        $stmt->bindValue(':cliente_id', $cliente_id);
        $stmt->bindValue(':actividad_id', $actividad_id);
        if ($fechaOc !== null) {
            $stmt->bindValue(':fecha_ocurrencia', $fechaOc);
        } else {
            $stmt->bindValue(':fecha_ocurrencia', null, PDO::PARAM_NULL);
        }

        return $stmt->execute();
    }

    public static function obtenerInscripciones()
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
        SELECT 
            i.id,
            a.id AS actividad_id,
            i.fecha_ocurrencia,
            i.asistio,
            a.nombre AS actividad,
            a.descripcion,
            a.fecha_inicio,
            a.fecha_fin,
            a.dia_semana,
            s.nombre AS sala,
            u.nombre AS monitor

        FROM inscripciones i

        INNER JOIN clientes c ON i.cliente_id = c.id
        INNER JOIN usuarios uc ON c.usuario_id = uc.id

        INNER JOIN actividades a ON i.actividad_id = a.id

        LEFT JOIN salas s ON a.sala_id = s.id
        LEFT JOIN monitores m ON a.monitor_id = m.id
        LEFT JOIN usuarios u ON m.usuario_id = u.id

        WHERE c.usuario_id = :usuario_id

        ORDER BY i.fecha_ocurrencia ASC, a.fecha_inicio ASC
    ");

        $stmt->bindValue(':usuario_id', $_SESSION['usuario_id']);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function cancelar($id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            DELETE FROM inscripciones WHERE id = :id
        ");

        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    /**
     * Solo borra si la inscripción pertenece al cliente vinculado al usuario en sesión.
     */
    /**
     * Mapa "actividadId_Y-m-d" => true para inscripciones del cliente en un rango de fechas (sesión).
     *
     * @return array<string, true>
     */
    public static function mapaInscripcionesClienteEnSemana(int $clienteId, string $weekStartYmd, string $weekEndYmd): array
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $weekStartYmd) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $weekEndYmd)) {
            return [];
        }

        $conexion = BasedeDatos::Conectar();
        $stmt = $conexion->prepare(
            'SELECT actividad_id, fecha_ocurrencia FROM inscripciones
             WHERE cliente_id = ?
               AND fecha_ocurrencia IS NOT NULL
               AND fecha_ocurrencia >= ?
               AND fecha_ocurrencia <= ?'
        );
        $stmt->execute([$clienteId, $weekStartYmd, $weekEndYmd]);
        $map = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $aid = (int) ($row['actividad_id'] ?? 0);
            $f = (string) ($row['fecha_ocurrencia'] ?? '');
            if ($aid > 0 && preg_match('/^\d{4}-\d{2}-\d{2}$/', $f)) {
                $map[$aid . '_' . $f] = true;
            }
        }

        return $map;
    }

    public static function cancelarParaUsuario(int $inscripcionId, int $usuarioId): bool
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare('
            DELETE i FROM inscripciones i
            INNER JOIN clientes c ON i.cliente_id = c.id
            WHERE i.id = :id AND c.usuario_id = :usuario_id
        ');
        $stmt->execute([
            ':id' => $inscripcionId,
            ':usuario_id' => $usuarioId,
        ]);

        return $stmt->rowCount() > 0;
    }
}
