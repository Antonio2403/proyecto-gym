<?php

class Comentario
{
    public static function finSesionTimestamp(array $actividad, string $fechaOcurrenciaYmd): ?int
    {
        if ($fechaOcurrenciaYmd === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaOcurrenciaYmd)) {
            return null;
        }
        $hora = date('H:i:s', strtotime($actividad['fecha_inicio']));
        $inicio = $fechaOcurrenciaYmd . ' ' . $hora;
        $dur = (int) ($actividad['duracion'] ?? 0);
        if ($dur <= 0) {
            $dur = 60;
        }

        return strtotime($inicio . " +{$dur} minutes");
    }

    public static function sesionHaPasado(array $actividad, string $fechaOcurrenciaYmd): bool
    {
        $fin = self::finSesionTimestamp($actividad, $fechaOcurrenciaYmd);

        return $fin !== null && time() > $fin;
    }

    public static function usuarioTieneReservaEnSesion(int $usuarioId, int $actividadId, string $fechaOcurrenciaYmd): bool
    {
        $conexion = BasedeDatos::Conectar();
        $stmt = $conexion->prepare("
            SELECT COUNT(*) FROM inscripciones i
            INNER JOIN clientes c ON i.cliente_id = c.id
            WHERE c.usuario_id = :usuario_id
            AND i.actividad_id = :actividad_id
            AND i.fecha_ocurrencia = :fecha_ocurrencia
        ");
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':actividad_id', $actividadId, PDO::PARAM_INT);
        $stmt->bindValue(':fecha_ocurrencia', $fechaOcurrenciaYmd);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    /** Reserva marcada como asistencia / apta para comentar esa sesión. */
    public static function usuarioAsistioSesionMarcada(int $usuarioId, int $actividadId, string $fechaOcurrenciaYmd): bool
    {
        $conexion = BasedeDatos::Conectar();
        $stmt = $conexion->prepare("
            SELECT COUNT(*) FROM inscripciones i
            INNER JOIN clientes c ON i.cliente_id = c.id
            WHERE c.usuario_id = :usuario_id
            AND i.actividad_id = :actividad_id
            AND i.fecha_ocurrencia = :fecha_ocurrencia
            AND i.asistio = 'S'
        ");
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':actividad_id', $actividadId, PDO::PARAM_INT);
        $stmt->bindValue(':fecha_ocurrencia', $fechaOcurrenciaYmd);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    /** @deprecated Usar usuarioAsistioSesionMarcada — se mantiene por compatibilidad. */
    public static function usuarioInscritoEnSesion(int $usuarioId, int $actividadId, string $fechaOcurrenciaYmd): bool
    {
        return self::usuarioAsistioSesionMarcada($usuarioId, $actividadId, $fechaOcurrenciaYmd);
    }

    /**
     * Conteos de comentarios por varias sesiones. Clave: "{id}|{Y-m-d}".
     *
     * @param array<int, array{actividad_id:int, fecha?:string, fecha_ocurrencia?:string}> $pares
     * @return array<string, int>
     */
    public static function contarPorSesiones(array $pares): array
    {
        $seen = [];
        $uniq = [];
        foreach ($pares as $p) {
            $aid = (int) ($p['actividad_id'] ?? 0);
            $fec = trim((string) ($p['fecha'] ?? $p['fecha_ocurrencia'] ?? ''));
            if ($aid <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fec)) {
                continue;
            }
            $k = $aid . '|' . $fec;
            if (isset($seen[$k])) {
                continue;
            }
            $seen[$k] = true;
            $uniq[] = ['actividad_id' => $aid, 'fecha' => $fec];
        }
        if ($uniq === []) {
            return [];
        }
        $conexion = BasedeDatos::Conectar();
        $ors = [];
        $params = [];
        foreach ($uniq as $i => $row) {
            $ors[] = '(actividad_id = :a' . $i . ' AND fecha_ocurrencia = :f' . $i . ')';
            $params['a' . $i] = $row['actividad_id'];
            $params['f' . $i] = $row['fecha'];
        }
        $sql = 'SELECT actividad_id, fecha_ocurrencia, COUNT(*) AS total FROM comentarios WHERE '
            . implode(' OR ', $ors) . ' GROUP BY actividad_id, fecha_ocurrencia';
        $stmt = $conexion->prepare($sql);
        $stmt->execute($params);
        $out = [];
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $out[(int) $r['actividad_id'] . '|' . $r['fecha_ocurrencia']] = (int) $r['total'];
        }

        return $out;
    }

    public static function contarPorSesion(int $actividadId, string $fechaOcurrenciaYmd): int
    {
        $conexion = BasedeDatos::Conectar();
        $stmt = $conexion->prepare('
            SELECT COUNT(*) FROM comentarios
            WHERE actividad_id = :actividad_id AND fecha_ocurrencia = :fecha_ocurrencia
        ');
        $stmt->bindValue(':actividad_id', $actividadId, PDO::PARAM_INT);
        $stmt->bindValue(':fecha_ocurrencia', $fechaOcurrenciaYmd);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * @param string $orden 'asc' = más antiguo primero, 'desc' = más reciente primero
     *
     * @return array{rows: array<int, array>, total: int, page: int, per_page: int, total_pages: int, orden: string}
     */
    public static function listarPorSesionPaginado(
        int $actividadId,
        string $fechaOcurrenciaYmd,
        int $page,
        int $perPage,
        string $orden = 'asc'
    ): array {
        $ordenNorm = ($orden === 'desc') ? 'desc' : 'asc';
        $orderBy = $ordenNorm === 'desc'
            ? 'c.fecha DESC, c.id DESC'
            : 'c.fecha ASC, c.id ASC';

        $perPage = min(50, max(5, $perPage));
        $page = max(1, $page);
        $total = self::contarPorSesion($actividadId, $fechaOcurrenciaYmd);
        $totalPages = $total > 0 ? (int) ceil($total / $perPage) : 1;
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        $conexion = BasedeDatos::Conectar();
        $sql = "
            SELECT c.id, c.texto, c.fecha, c.fecha_ocurrencia,
                   u.nombre AS autor_nombre, u.apellido1 AS autor_apellido1
            FROM comentarios c
            INNER JOIN clientes cl ON c.cliente_id = cl.id
            INNER JOIN usuarios u ON cl.usuario_id = u.id
            WHERE c.actividad_id = :actividad_id
            AND c.fecha_ocurrencia = :fecha_ocurrencia
            ORDER BY {$orderBy}
            LIMIT :lim OFFSET :off
        ";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':actividad_id', $actividadId, PDO::PARAM_INT);
        $stmt->bindValue(':fecha_ocurrencia', $fechaOcurrenciaYmd);
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'orden' => $ordenNorm,
        ];
    }

    public static function crear(int $clienteId, int $actividadId, string $fechaOcurrenciaYmd, string $texto): bool
    {
        $conexion = BasedeDatos::Conectar();
        $stmt = $conexion->prepare("
            INSERT INTO comentarios (cliente_id, actividad_id, fecha_ocurrencia, texto)
            VALUES (:cliente_id, :actividad_id, :fecha_ocurrencia, :texto)
        ");
        $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
        $stmt->bindValue(':actividad_id', $actividadId, PDO::PARAM_INT);
        $stmt->bindValue(':fecha_ocurrencia', $fechaOcurrenciaYmd);
        $stmt->bindValue(':texto', $texto);

        return $stmt->execute();
    }
}
