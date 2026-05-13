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

    /**
     * @return array<int, array{email: string, nombre: string, fecha_ocurrencia: ?string}>
     */
    public static function listarInscritosConEmailPorActividad(int $actividadId): array
    {
        $conexion = BasedeDatos::Conectar();
        $stmt = $conexion->prepare(
            'SELECT DISTINCT u.email, u.nombre, i.fecha_ocurrencia
             FROM inscripciones i
             INNER JOIN clientes c ON c.id = i.cliente_id
             INNER JOIN usuarios u ON u.id = c.usuario_id
             WHERE i.actividad_id = ?
             ORDER BY i.fecha_ocurrencia ASC, u.nombre ASC'
        );
        $stmt->execute([$actividadId]);
        $rows = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $rows[] = [
                'email' => (string) ($r['email'] ?? ''),
                'nombre' => (string) ($r['nombre'] ?? ''),
                'fecha_ocurrencia' => isset($r['fecha_ocurrencia']) ? (string) $r['fecha_ocurrencia'] : null,
            ];
        }

        return $rows;
    }

    public static function eliminarTodasPorActividad(int $actividadId): void
    {
        $conexion = BasedeDatos::Conectar();
        $stmt = $conexion->prepare('DELETE FROM inscripciones WHERE actividad_id = ?');
        $stmt->execute([$actividadId]);
    }

    /**
     * Inscritos de una actividad en un rango de fechas (para panel monitor).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function listarPorActividadEnRango(int $actividadId, string $desdeYmd, string $hastaYmd): array
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $desdeYmd) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $hastaYmd)) {
            return [];
        }

        $conexion = BasedeDatos::Conectar();
        $stmt = $conexion->prepare(
            'SELECT i.id, i.fecha_ocurrencia, i.fecha_inscripcion, u.nombre, u.apellido1, u.email, u.telefono
             FROM inscripciones i
             INNER JOIN clientes c ON c.id = i.cliente_id
             INNER JOIN usuarios u ON u.id = c.usuario_id
             WHERE i.actividad_id = ?
               AND i.fecha_ocurrencia IS NOT NULL
               AND i.fecha_ocurrencia >= ?
               AND i.fecha_ocurrencia <= ?
             ORDER BY i.fecha_ocurrencia ASC, u.nombre ASC'
        );
        $stmt->execute([$actividadId, $desdeYmd, $hastaYmd]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Sesiones (actividad + fecha) del monitor con inscritos, paginadas y filtrables.
     *
     * @param array{q?: string|null, actividad?: string|null, sala?: string|null, cliente?: string|null, email?: string|null, fecha_desde?: string|null, fecha_hasta?: string|null, dia_semana?: string|null} $f
     *
     * @return array{rows: array<int, array<string,mixed>>, total: int, page: int, per_page: int, total_pages: int}
     */
    public static function buscarSesionesMonitorPaginado(int $monitorId, int $page, int $perPage, array $f): array
    {
        if ($monitorId <= 0) {
            return ['rows' => [], 'total' => 0, 'page' => 1, 'per_page' => $perPage, 'total_pages' => 1];
        }

        $page = max(1, $page);
        $perPage = min(50, max(5, $perPage));

        $tz = new DateTimeZone(date_default_timezone_get());
        $hoy = (new DateTimeImmutable('today', $tz))->format('Y-m-d');
        $defHasta = (new DateTimeImmutable('today', $tz))->modify('+13 days')->format('Y-m-d');

        $fechaDesde = gp_grid_date_opt($f['fecha_desde'] ?? null) ?? $hoy;
        $fechaHasta = gp_grid_date_opt($f['fecha_hasta'] ?? null) ?? $defHasta;
        if ($fechaDesde > $fechaHasta) {
            [$fechaDesde, $fechaHasta] = [$fechaHasta, $fechaDesde];
        }

        $bind = [
            ':mid' => $monitorId,
            ':fdesde' => $fechaDesde,
            ':fhasta' => $fechaHasta,
        ];
        $conds = [
            'a.monitor_id = :mid',
            'i.fecha_ocurrencia IS NOT NULL',
            'i.fecha_ocurrencia >= :fdesde',
            'i.fecha_ocurrencia <= :fhasta',
        ];

        $qLike = gp_grid_like_contains(gp_grid_str($f['q'] ?? null));
        if ($qLike !== null) {
            $conds[] = '(IFNULL(a.nombre, \'\') LIKE :q_act OR IFNULL(s.nombre, \'\') LIKE :q_sala OR IFNULL(u.nombre, \'\') LIKE :q_nom OR IFNULL(u.apellido1, \'\') LIKE :q_ap1 OR IFNULL(u.apellido2, \'\') LIKE :q_ap2 OR IFNULL(u.email, \'\') LIKE :q_mail)';
            $bind[':q_act'] = $qLike;
            $bind[':q_sala'] = $qLike;
            $bind[':q_nom'] = $qLike;
            $bind[':q_ap1'] = $qLike;
            $bind[':q_ap2'] = $qLike;
            $bind[':q_mail'] = $qLike;
        }
        if (($nom = gp_grid_like_contains(gp_grid_str($f['actividad'] ?? null))) !== null) {
            $conds[] = 'IFNULL(a.nombre, \'\') LIKE :f_act';
            $bind[':f_act'] = $nom;
        }
        if (($sala = gp_grid_like_contains(gp_grid_str($f['sala'] ?? null))) !== null) {
            $conds[] = 'IFNULL(s.nombre, \'\') LIKE :f_sala';
            $bind[':f_sala'] = $sala;
        }
        if (($cli = gp_grid_like_contains(gp_grid_str($f['cliente'] ?? null))) !== null) {
            $conds[] = '(IFNULL(u.nombre, \'\') LIKE :f_cli_nom OR IFNULL(u.apellido1, \'\') LIKE :f_cli_ap1 OR IFNULL(u.apellido2, \'\') LIKE :f_cli_ap2)';
            $bind[':f_cli_nom'] = $cli;
            $bind[':f_cli_ap1'] = $cli;
            $bind[':f_cli_ap2'] = $cli;
        }
        if (($mail = gp_grid_like_contains(gp_grid_str($f['email'] ?? null))) !== null) {
            $conds[] = 'IFNULL(u.email, \'\') LIKE :f_mail';
            $bind[':f_mail'] = $mail;
        }

        $dia = gp_grid_str($f['dia_semana'] ?? null);
        $diaMap = ['L' => 0, 'M' => 1, 'X' => 2, 'J' => 3, 'V' => 4, 'S' => 5, 'D' => 6];
        if ($dia !== null && strlen($dia) === 1) {
            $diaU = strtoupper($dia);
            if (isset($diaMap[$diaU])) {
                $conds[] = 'WEEKDAY(i.fecha_ocurrencia) = :f_dia';
                $bind[':f_dia'] = $diaMap[$diaU];
            }
        }

        $from = ' FROM inscripciones i
            INNER JOIN actividades a ON a.id = i.actividad_id
            LEFT JOIN salas s ON s.id = a.sala_id
            INNER JOIN clientes c ON c.id = i.cliente_id
            INNER JOIN usuarios u ON u.id = c.usuario_id ';
        $where = ' WHERE ' . implode(' AND ', $conds);

        $conexion = BasedeDatos::Conectar();

        $sqlCount = 'SELECT COUNT(DISTINCT CONCAT(i.actividad_id, \'|\', i.fecha_ocurrencia)) ' . $from . $where;
        $st = $conexion->prepare($sqlCount);
        foreach ($bind as $k => $v) {
            $st->bindValue($k, $v);
        }
        $st->execute();
        $total = (int) $st->fetchColumn();

        $totalPages = $total > 0 ? max(1, (int) ceil($total / $perPage)) : 1;
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT i.actividad_id, i.fecha_ocurrencia,
                a.nombre AS actividad_nombre, a.fecha_inicio, a.duracion, a.recurrente,
                s.nombre AS sala_nombre,
                COUNT(i.id) AS num_inscritos
            ' . $from . $where . '
            GROUP BY i.actividad_id, i.fecha_ocurrencia, a.nombre, a.fecha_inicio, a.duracion, a.recurrente, s.nombre
            ORDER BY i.fecha_ocurrencia ASC, a.nombre ASC
            LIMIT :lim OFFSET :off';

        $st = $conexion->prepare($sql);
        foreach ($bind as $k => $v) {
            $st->bindValue($k, $v);
        }
        $st->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $st->bindValue(':off', $offset, PDO::PARAM_INT);
        $st->execute();
        $sesiones = $st->fetchAll(PDO::FETCH_ASSOC);

        if ($sesiones === []) {
            return [
                'rows' => [],
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => $totalPages,
            ];
        }

        $pares = [];
        foreach ($sesiones as $s) {
            $pares[] = [
                'actividad_id' => (int) $s['actividad_id'],
                'fecha' => (string) $s['fecha_ocurrencia'],
            ];
        }
        $inscritosMap = self::listarInscritosPorSesiones($pares);

        $rows = [];
        foreach ($sesiones as $s) {
            $aid = (int) $s['actividad_id'];
            $fec = (string) $s['fecha_ocurrencia'];
            $key = $aid . '|' . $fec;
            $rows[] = [
                'actividad_id' => $aid,
                'fecha_ocurrencia' => $fec,
                'actividad_nombre' => (string) ($s['actividad_nombre'] ?? ''),
                'hora' => !empty($s['fecha_inicio']) ? date('H:i', strtotime((string) $s['fecha_inicio'])) : '',
                'recurrente' => (int) ($s['recurrente'] ?? 1),
                'sala_nombre' => (string) ($s['sala_nombre'] ?? ''),
                'inscritos' => $inscritosMap[$key] ?? [],
                'total_inscritos' => (int) ($s['num_inscritos'] ?? 0),
            ];
        }

        return [
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
        ];
    }

    /**
     * @param array<int, array{actividad_id: int, fecha: string}> $pares
     * @return array<string, array<int, array<string, mixed>>> clave actividadId|fecha => inscritos
     */
    public static function listarInscritosPorSesiones(array $pares): array
    {
        $uniq = [];
        foreach ($pares as $p) {
            $aid = (int) ($p['actividad_id'] ?? 0);
            $fec = trim((string) ($p['fecha'] ?? ''));
            if ($aid <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fec)) {
                continue;
            }
            $uniq[$aid . '|' . $fec] = ['actividad_id' => $aid, 'fecha' => $fec];
        }
        if ($uniq === []) {
            return [];
        }

        $conexion = BasedeDatos::Conectar();
        $ors = [];
        $bind = [];
        $i = 0;
        foreach ($uniq as $row) {
            $ors[] = '(i.actividad_id = :a' . $i . ' AND i.fecha_ocurrencia = :f' . $i . ')';
            $bind['a' . $i] = $row['actividad_id'];
            $bind['f' . $i] = $row['fecha'];
            ++$i;
        }

        $sql = 'SELECT i.actividad_id, i.fecha_ocurrencia, i.id, u.nombre, u.apellido1, u.email, u.telefono
            FROM inscripciones i
            INNER JOIN clientes c ON c.id = i.cliente_id
            INNER JOIN usuarios u ON u.id = c.usuario_id
            WHERE ' . implode(' OR ', $ors) . '
            ORDER BY i.fecha_ocurrencia ASC, u.nombre ASC';

        $st = $conexion->prepare($sql);
        foreach ($bind as $k => $v) {
            $st->bindValue(':' . $k, $v);
        }
        $st->execute();

        $map = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $key = (int) $r['actividad_id'] . '|' . (string) $r['fecha_ocurrencia'];
            $map[$key][] = $r;
        }

        return $map;
    }

    /**
     * Salas distintas en actividades del monitor (para filtro).
     *
     * @return array<int, string>
     */
    public static function salasDistintasMonitor(int $monitorId): array
    {
        if ($monitorId <= 0) {
            return [];
        }
        $conexion = BasedeDatos::Conectar();
        $st = $conexion->prepare(
            'SELECT DISTINCT s.nombre FROM actividades a
             INNER JOIN salas s ON s.id = a.sala_id
             WHERE a.monitor_id = ? AND s.nombre IS NOT NULL AND s.nombre <> \'\'
             ORDER BY s.nombre ASC'
        );
        $st->execute([$monitorId]);

        return array_map(static fn (array $r): string => (string) $r['nombre'], $st->fetchAll(PDO::FETCH_ASSOC));
    }
}
