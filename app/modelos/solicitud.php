<?php

class Solicitud
{
    /** Etiqueta y variante CSS para el estado P / A / R */
    public static function metaEstado(string $est): array
    {
        switch ($est) {
            case 'P':
                return ['label' => 'Pendiente', 'variant' => 'pendiente'];
            case 'A':
                return ['label' => 'Aprobada', 'variant' => 'aprobada'];
            case 'R':
                return ['label' => 'Rechazada', 'variant' => 'rechazada'];
            default:
                return ['label' => $est, 'variant' => 'otro'];
        }
    }

    public static function crear($monitor_id, $tipo, ?string $descripcion = null)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            INSERT INTO solicitudes (monitor_id, tipo, descripcion)
            VALUES (:monitor_id, :tipo, :descripcion)
        ");

        return $stmt->execute([
            ':monitor_id' => $monitor_id,
            ':tipo' => $tipo,
            ':descripcion' => $descripcion,
        ]);
    }

    public static function obtenerPendientes()
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->query("
            SELECT s.*, u.nombre AS monitor_nombre, u.apellido1 AS monitor_apellido1, u.email AS monitor_email
            FROM solicitudes s
            INNER JOIN monitores m ON s.monitor_id = m.id
            INNER JOIN usuarios u ON m.usuario_id = u.id
            WHERE s.estado = 'P'
            ORDER BY s.fecha_creacion ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerAprobadas()
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->query("
            SELECT s.*, u.nombre AS monitor_nombre, u.apellido1 AS monitor_apellido1, u.email AS monitor_email
            FROM solicitudes s
            INNER JOIN monitores m ON s.monitor_id = m.id
            INNER JOIN usuarios u ON m.usuario_id = u.id
            WHERE s.estado = 'A'
            ORDER BY s.fecha_revision DESC, s.fecha_creacion DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerRechazadas()
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->query("
            SELECT s.*, u.nombre AS monitor_nombre, u.apellido1 AS monitor_apellido1, u.email AS monitor_email
            FROM solicitudes s
            INNER JOIN monitores m ON s.monitor_id = m.id
            INNER JOIN usuarios u ON m.usuario_id = u.id
            WHERE s.estado = 'R'
            ORDER BY s.fecha_revision DESC, s.fecha_creacion DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Años con reportes, incluyendo el año en curso.
     *
     * @return array<int, int>
     */
    public static function aniosDisponibles(): array
    {
        $conexion = BasedeDatos::Conectar();
        $rows = $conexion->query('SELECT DISTINCT YEAR(fecha_creacion) AS anio FROM solicitudes WHERE fecha_creacion IS NOT NULL ORDER BY anio DESC');
        $anios = [];
        if ($rows) {
            foreach ($rows->fetchAll(PDO::FETCH_COLUMN) as $anio) {
                $n = (int) $anio;
                if ($n > 0) {
                    $anios[] = $n;
                }
            }
        }
        $actual = (int) date('Y');
        if (!in_array($actual, $anios, true)) {
            $anios[] = $actual;
        }
        rsort($anios);

        return $anios;
    }

    /**
     * @param array{q?: string|null, tipo?: string|null, monitor?: string|null, anio?: string|null, mes?: string|null, recientes?: string|null} $f
     *
     * @return array{rows: array<int, array<string,mixed>>, total: int, page: int, per_page: int, total_pages: int}
     */
    public static function buscarPaginado(string $estado, int $page, int $perPage, array $f): array
    {
        $estado = strtoupper(trim($estado));
        $todos = $estado === 'T';
        if (!$todos && !in_array($estado, ['P', 'A', 'R'], true)) {
            $estado = 'P';
        }

        $recientes = (int) ($f['recientes'] ?? 0);
        $page = max(1, $page);
        if ($recientes > 0) {
            $perPage = min(10, max(1, $recientes));
            $page = 1;
        } else {
            $perPage = min(50, max(5, $perPage));
        }

        $conexion = BasedeDatos::Conectar();
        $bind = [];
        $conds = [];
        if (!$todos) {
            $conds[] = 's.estado = :est';
            $bind[':est'] = $estado;
        }

        $qLike = gp_grid_like_contains(gp_grid_str($f['q'] ?? null));
        if ($qLike !== null) {
            $conds[] = '(IFNULL(s.tipo, \'\') LIKE :q OR IFNULL(s.descripcion, \'\') LIKE :q OR u.nombre LIKE :q OR u.apellido1 LIKE :q OR IFNULL(u.apellido2, \'\') LIKE :q OR u.email LIKE :q)';
            $bind[':q'] = $qLike;
        }
        if (($tipo = gp_grid_like_contains(gp_grid_str($f['tipo'] ?? null))) !== null) {
            $conds[] = 'IFNULL(s.tipo, \'\') LIKE :ft';
            $bind[':ft'] = $tipo;
        }
        if (($mon = gp_grid_like_contains(gp_grid_str($f['monitor'] ?? null))) !== null) {
            $conds[] = '(u.nombre LIKE :mon OR u.apellido1 LIKE :mon OR IFNULL(u.apellido2, \'\') LIKE :mon OR u.email LIKE :mon)';
            $bind[':mon'] = $mon;
        }
        if ($recientes <= 0) {
            $mes = (int) ($f['mes'] ?? 0);
            $anio = (int) ($f['anio'] ?? 0);
            if ($mes >= 1 && $mes <= 12) {
                $conds[] = 'MONTH(s.fecha_creacion) = :mes';
                $bind[':mes'] = $mes;
            } else {
                if ($anio < 2000 || $anio > 2100) {
                    $anio = (int) date('Y');
                }
                $conds[] = 'YEAR(s.fecha_creacion) = :anio';
                $bind[':anio'] = $anio;
            }
        }

        $baseFrom = ' FROM solicitudes s
            INNER JOIN monitores m ON s.monitor_id = m.id
            INNER JOIN usuarios u ON m.usuario_id = u.id ';

        $where = $conds !== [] ? ' WHERE ' . implode(' AND ', $conds) : '';
        $order = ($recientes > 0 || $todos)
            ? ' ORDER BY s.fecha_creacion DESC'
            : ($estado === 'P'
                ? ' ORDER BY s.fecha_creacion DESC'
                : ' ORDER BY s.fecha_revision DESC, s.fecha_creacion DESC');

        $stmt = gp_grid_prepare($conexion, 'SELECT COUNT(*) ' . $baseFrom . $where, $bind);
        $stmt->execute();
        $total = (int) $stmt->fetchColumn();

        $totalPages = $total > 0 ? max(1, (int) ceil($total / $perPage)) : 1;
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sel = 'SELECT s.*, u.nombre AS monitor_nombre, u.apellido1 AS monitor_apellido1, u.apellido2 AS monitor_apellido2, u.email AS monitor_email';
        $sql = $sel . $baseFrom . $where . $order . ' LIMIT :lim OFFSET :off';
        $stmt = gp_grid_prepare($conexion, $sql, $bind);
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

    /**
     * Lista solicitudes del monitor actual (por usuario_id de sesión).
     */
    public static function obtenerPorMonitor(int $usuarioMonitorId)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            SELECT s.*
            FROM solicitudes s
            INNER JOIN monitores m ON s.monitor_id = m.id
            WHERE m.usuario_id = :usuario_id
            ORDER BY s.fecha_creacion DESC
        ");

        $stmt->execute([':usuario_id' => $usuarioMonitorId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function obtenerAdministradorDbIdPorUsuario(int $usuarioId): ?int
    {
        $conexion = BasedeDatos::Conectar();
        $stmt = $conexion->prepare('SELECT id FROM administradores WHERE usuario_id = ? LIMIT 1');
        $stmt->execute([$usuarioId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int) $row['id'] : null;
    }

    public static function cambiarEstado($id, $estado, $adminUsuarioId)
    {
        $adminDbId = self::obtenerAdministradorDbIdPorUsuario((int) $adminUsuarioId);
        if ($adminDbId === null) {
            return false;
        }

        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            UPDATE solicitudes
            SET estado = :estado, admin_id = :admin_id, fecha_revision = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':estado' => $estado,
            ':admin_id' => $adminDbId,
            ':id' => $id,
        ]);
    }
}
