<?php

class Actividad
{
    private static function ordenDias(array $dias): array
    {
        $valid = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
        $dias = array_values(array_unique(array_intersect($valid, array_map('strval', $dias))));
        $order = ['L' => 0, 'M' => 1, 'X' => 2, 'J' => 3, 'V' => 4, 'S' => 5, 'D' => 6];
        usort($dias, static function (string $a, string $b) use ($order): int {
            return ($order[$a] ?? 99) <=> ($order[$b] ?? 99);
        });

        return $dias;
    }

    /**
     * @param string[] $diasLetras L,M,...
     */
    public static function sincronizarDias(int $actividadId, array $diasLetras): void
    {
        $diasLetras = self::ordenDias(array_values(array_filter($diasLetras)));
        if ($diasLetras === []) {
            return;
        }

        $conexion = BasedeDatos::Conectar();
        $conexion->prepare('DELETE FROM actividad_dias WHERE actividad_id = ?')->execute([$actividadId]);
        $ins = $conexion->prepare('INSERT INTO actividad_dias (actividad_id, dia_semana) VALUES (?, ?)');
        foreach ($diasLetras as $d) {
            $ins->execute([$actividadId, $d]);
        }
        $conexion->prepare('UPDATE actividades SET dia_semana = ? WHERE id = ?')->execute([$diasLetras[0], $actividadId]);
    }

    /** @return string[] */
    public static function diasParaActividadId(int $actividadId): array
    {
        $conexion = BasedeDatos::Conectar();
        $st = $conexion->prepare(
            'SELECT dia_semana FROM actividad_dias WHERE actividad_id = ? ORDER BY FIELD(dia_semana,\'L\',\'M\',\'X\',\'J\',\'V\',\'S\',\'D\')'
        );
        $st->execute([$actividadId]);
        $out = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $out[] = (string) $r['dia_semana'];
        }
        if ($out === []) {
            $a = self::obtenerPorId($actividadId);
            $d = $a['dia_semana'] ?? null;
            if ($d !== null && $d !== '') {
                return [(string) $d];
            }
        }

        return $out;
    }

    /** @param array<int, array<string,mixed>> $rows */
    public static function adjuntarDiasFilas(array $rows): array
    {
        if ($rows === []) {
            return $rows;
        }
        $ids = array_map(static fn (array $r): int => (int) $r['id'], $rows);
        $conexion = BasedeDatos::Conectar();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $st = $conexion->prepare("SELECT actividad_id, dia_semana FROM actividad_dias WHERE actividad_id IN ($placeholders) ORDER BY FIELD(dia_semana,'L','M','X','J','V','S','D')");
        $st->execute($ids);
        $map = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $aid = (int) $r['actividad_id'];
            $map[$aid][] = (string) $r['dia_semana'];
        }
        foreach ($rows as &$r) {
            $id = (int) $r['id'];
            $r['dias'] = $map[$id] ?? (isset($r['dia_semana']) && $r['dia_semana'] !== '' ? [(string) $r['dia_semana']] : []);
        }
        unset($r);

        return $rows;
    }

    public static function fechaEsSesionValida(int $actividadId, string $fechaYmd): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaYmd)) {
            return false;
        }
        $act = self::obtenerPorId($actividadId);
        if (!$act) {
            return false;
        }
        $rec = (int) ($act['recurrente'] ?? 1);
        $dias = self::diasParaActividadId($actividadId);
        $n = (int) (new DateTimeImmutable($fechaYmd))->format('N');
        $cod = [1 => 'L', 2 => 'M', 3 => 'X', 4 => 'J', 5 => 'V', 6 => 'S', 7 => 'D'][$n] ?? '';

        if ($rec === 0) {
            $fi = substr((string) ($act['fecha_inicio'] ?? ''), 0, 10);

            return $fi === $fechaYmd;
        }

        return $cod !== '' && in_array($cod, $dias, true);
    }

    /**
     * @param string[] $diasLetras
     * @return int|false id actividad
     */
    public static function guardar($nombre, $descripcion, $duracion, $monitor_id, $sala_id, $fecha_inicio, $fecha_fin, $dia_semana, array $diasLetras = [])
    {
        $conexion = BasedeDatos::Conectar();

        $diasOk = self::ordenDias($diasLetras !== [] ? $diasLetras : [$dia_semana]);
        if ($diasOk === []) {
            return false;
        }
        $primero = $diasOk[0];

        $stmt = $conexion->prepare("
        INSERT INTO actividades 
        (nombre, descripcion, duracion, monitor_id, sala_id, fecha_inicio, fecha_fin, dia_semana)
        VALUES (:nombre, :descripcion, :duracion, :monitor_id, :sala_id, :fecha_inicio, :fecha_fin, :dia_semana)
    ");

        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':descripcion', $descripcion);
        $stmt->bindValue(':duracion', $duracion);
        $stmt->bindValue(':monitor_id', $monitor_id);
        $stmt->bindValue(':sala_id', $sala_id);
        $stmt->bindValue(':fecha_inicio', $fecha_inicio);
        $stmt->bindValue(':fecha_fin', $fecha_fin);
        $stmt->bindValue(':dia_semana', $primero);

        if (!$stmt->execute()) {
            return false;
        }
        $id = (int) $conexion->lastInsertId();
        self::sincronizarDias($id, $diasOk);

        return $id;
    }

    public static function obtenerPorSala($sala_id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            SELECT * FROM actividades WHERE sala_id = :sala_id
        ");

        $stmt->bindValue(':sala_id', $sala_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId($id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            SELECT * FROM actividades WHERE id = :id
        ");

        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function actualizar($id, $nombre, $descripcion, $sala_id = null, $monitor_id = null)
    {
        $conexion = BasedeDatos::Conectar();

        if ($sala_id !== null && $monitor_id !== null) {
            $stmt = $conexion->prepare("
                UPDATE actividades
                SET nombre = :nombre,
                    descripcion = :descripcion,
                    sala_id = :sala_id,
                    monitor_id = :monitor_id
                WHERE id = :id
            ");
            $stmt->bindValue(':sala_id', $sala_id);
            $stmt->bindValue(':monitor_id', $monitor_id);
        } else {
            $stmt = $conexion->prepare("
                UPDATE actividades SET nombre = :nombre, descripcion = :descripcion WHERE id = :id
            ");
        }

        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':descripcion', $descripcion);

        return $stmt->execute();
    }

    /**
     * Actualiza horario recurrente, duración y días (checkboxes).
     *
     * @param string[] $diasLetras
     */
    public static function actualizarHorario(int $id, $nombre, $descripcion, $sala_id, $monitor_id, int $duracion, string $hora_inicio, int $recurrente, array $diasLetras): bool
    {
        $diasLetras = self::ordenDias($diasLetras);
        if ($diasLetras === [] || $duracion < 1 || $duracion > 600) {
            return false;
        }
        $fecha_base = date('Y-m-d');
        $fecha_inicio = $fecha_base . ' ' . $hora_inicio . ':00';
        $fecha_fin = date('Y-m-d H:i:s', strtotime($fecha_inicio . ' +' . $duracion . ' minutes'));

        $conexion = BasedeDatos::Conectar();
        $stmt = $conexion->prepare(
            'UPDATE actividades SET nombre = :nombre, descripcion = :descripcion, sala_id = :sala_id, monitor_id = :monitor_id,
             duracion = :duracion, fecha_inicio = :fi, fecha_fin = :ff, recurrente = :rec, dia_semana = :dia
             WHERE id = :id'
        );
        $ok = $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':sala_id' => $sala_id,
            ':monitor_id' => $monitor_id,
            ':duracion' => $duracion,
            ':fi' => $fecha_inicio,
            ':ff' => $fecha_fin,
            ':rec' => $recurrente ? 1 : 0,
            ':dia' => $diasLetras[0],
            ':id' => $id,
        ]);
        if ($ok) {
            self::sincronizarDias((int) $id, $diasLetras);
        }

        return $ok;
    }

    public static function obtenerTodas()
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->query("
            SELECT a.*, s.nombre AS sala_nombre, u.nombre AS monitor_nombre
            FROM actividades a
            LEFT JOIN salas s ON a.sala_id = s.id
            LEFT JOIN monitores m ON a.monitor_id = m.id
            LEFT JOIN usuarios u ON m.usuario_id = u.id
        ");

        return self::adjuntarDiasFilas($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function eliminar($id)
    {
        $conexion = BasedeDatos::Conectar();

        try {
            $conexion->beginTransaction();

            $stmt = $conexion->prepare("DELETE FROM comentarios WHERE actividad_id = :id");
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $conexion->prepare("DELETE FROM inscripciones WHERE actividad_id = :id");
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $conexion->prepare("DELETE FROM actividad_dias WHERE actividad_id = :id");
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $conexion->prepare("DELETE FROM actividades WHERE id = :id");
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
            $ok = $stmt->execute();

            $conexion->commit();
            return $ok;
        } catch (Throwable $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log('[Actividad] eliminar: ' . $e->getMessage());
            return false;
        }
    }
    public static function contarInscritos($actividad_id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
        SELECT COUNT(*) as total 
        FROM inscripciones 
        WHERE actividad_id = ?
    ");

        $stmt->execute([$actividad_id]);
        return $stmt->fetch()['total'];
    }

    /**
     * @param int[] $ids
     * @return array<int, int> actividad_id => número de inscritos
     */
    public static function contarInscritosPorActividades(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($ids === []) {
            return [];
        }

        $conexion = BasedeDatos::Conectar();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $conexion->prepare(
            "SELECT actividad_id, COUNT(*) AS total FROM inscripciones WHERE actividad_id IN ($placeholders) GROUP BY actividad_id"
        );
        $stmt->execute($ids);

        $out = array_fill_keys($ids, 0);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $out[(int) $row['actividad_id']] = (int) $row['total'];
        }

        return $out;
    }
    public static function yaInscrito($cliente_id, $actividad_id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
        SELECT id FROM inscripciones 
        WHERE cliente_id = ? AND actividad_id = ?
    ");

        $stmt->execute([$cliente_id, $actividad_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    /**
     * @param array{sala_id?: int|null, monitor_id?: int|null, nombre?: string|null, dia_semana?: string|null, recurrente?: int|null, q?: string|null} $f
     *
     * @return array{rows: array<int, array<string,mixed>>, total: int, page: int, per_page: int, total_pages: int}
     */
    public static function buscarPaginadoAdmin(int $page, int $perPage, array $f): array
    {
        $page = max(1, $page);
        $perPage = min(50, max(5, $perPage));

        $conexion = BasedeDatos::Conectar();
        $bind = [];
        $conds = [];

        $baseFrom = ' FROM actividades a
            LEFT JOIN salas s ON a.sala_id = s.id
            LEFT JOIN monitores m ON a.monitor_id = m.id
            LEFT JOIN usuarios u ON m.usuario_id = u.id ';

        $qLike = gp_grid_like_contains(gp_grid_str($f['q'] ?? null));
        if ($qLike !== null) {
            $conds[] = '(IFNULL(a.nombre, \'\') LIKE :q OR IFNULL(a.descripcion, \'\') LIKE :q OR IFNULL(s.nombre, \'\') LIKE :q OR IFNULL(u.nombre, \'\') LIKE :q)';
            $bind[':q'] = $qLike;
        }
        if (($nom = gp_grid_like_contains(gp_grid_str($f['nombre'] ?? null))) !== null) {
            $conds[] = 'IFNULL(a.nombre, \'\') LIKE :f_nom';
            $bind[':f_nom'] = $nom;
        }
        $salaId = isset($f['sala_id']) ? (int) $f['sala_id'] : 0;
        if ($salaId > 0) {
            $conds[] = 'a.sala_id = :sid';
            $bind[':sid'] = $salaId;
        }
        $monId = isset($f['monitor_id']) ? (int) $f['monitor_id'] : 0;
        if ($monId > 0) {
            $conds[] = 'a.monitor_id = :mid';
            $bind[':mid'] = $monId;
        }
        $dia = gp_grid_str($f['dia_semana'] ?? null);
        if ($dia !== null && strlen($dia) === 1) {
            $diaU = strtoupper($dia);
            $conds[] = '(EXISTS (SELECT 1 FROM actividad_dias adg WHERE adg.actividad_id = a.id AND adg.dia_semana = :dia)
                OR (NOT EXISTS (SELECT 1 FROM actividad_dias adg2 WHERE adg2.actividad_id = a.id) AND a.dia_semana = :dia2))';
            $bind[':dia'] = $diaU;
            $bind[':dia2'] = $diaU;
        }
        $rec = array_key_exists('recurrente', $f) ? gp_grid_recurrent_opt((string) $f['recurrente']) : null;
        if ($rec !== null) {
            $conds[] = 'a.recurrente = :rec';
            $bind[':rec'] = $rec;
        }

        $where = $conds !== [] ? ' WHERE ' . implode(' AND ', $conds) : '';
        $sel = 'SELECT a.*, s.nombre AS sala_nombre, u.nombre AS monitor_nombre';

        $stmt = $conexion->prepare('SELECT COUNT(*) ' . $baseFrom . $where);
        foreach ($bind as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $total = (int) $stmt->fetchColumn();

        $totalPages = $total > 0 ? max(1, (int) ceil($total / $perPage)) : 1;
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = $sel . $baseFrom . $where . ' ORDER BY a.id DESC LIMIT :lim OFFSET :off';
        $stmt = $conexion->prepare($sql);
        foreach ($bind as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
        ];
    }
}
