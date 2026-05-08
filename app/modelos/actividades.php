<?php

class Actividad
{
    public static function guardar($nombre, $descripcion, $duracion, $monitor_id, $sala_id, $fecha_inicio, $fecha_fin, $dia_semana)
    {
        $conexion = BasedeDatos::Conectar();

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
        $stmt->bindValue(':dia_semana', $dia_semana);

        return $stmt->execute();
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

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function eliminar($id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("DELETE FROM actividades WHERE id = :id");
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
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

        return $stmt->rowCount() > 0;
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
            $conds[] = 'a.dia_semana = :dia';
            $bind[':dia'] = strtoupper($dia);
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
