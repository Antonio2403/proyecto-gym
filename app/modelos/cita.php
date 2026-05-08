<?php

class Cita
{
    public static function crear(int $clienteId, int $fisioId, string $fechaHora, string $motivo): bool
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'INSERT INTO citas (cliente_id, fisio_id, fecha, motivo, estado)
             VALUES (?, ?, ?, ?, \'S\')'
        );

        return $st->execute([$clienteId, $fisioId, $fechaHora, $motivo]);
    }

    /**
     * Citas del cliente con datos del fisioterapeuta.
     */
    public static function listarPorCliente(int $clienteId): array
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'SELECT c.id, c.fecha, c.motivo, c.estado, f.nombre AS fisio_nombre, f.especialidad
             FROM citas c
             INNER JOIN fisioterapeutas f ON f.id = c.fisio_id
             WHERE c.cliente_id = ?
             ORDER BY c.fecha DESC'
        );
        $st->execute([$clienteId]);

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function perteneceACliente(int $citaId, int $clienteId): bool
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare('SELECT id FROM citas WHERE id = ? AND cliente_id = ? LIMIT 1');
        $st->execute([$citaId, $clienteId]);

        return (bool) $st->fetchColumn();
    }

    /**
     * Cancela una cita solicitada (S). Devuelve true si hubo fila afectada.
     */
    public static function cancelarSiSolicitada(int $citaId, int $clienteId): bool
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            "UPDATE citas SET estado = 'CA' WHERE id = ? AND cliente_id = ? AND estado = 'S'"
        );
        $st->execute([$citaId, $clienteId]);

        return $st->rowCount() > 0;
    }

    /**
     * Listado para panel del fisioterapeuta: paginación, filtros y datos del socio.
     *
     * $f: estado?, fecha_desde?, fecha_hasta?, motivo?, cliente?, solo_futuras? ('1' = solo desde ahora)
     *
     * @return array{rows: array<int, array<string,mixed>>, total: int, page: int, per_page: int, total_pages: int}
     */
    public static function buscarPorFisioterapeutaPaginado(int $fisioId, int $page, int $perPage, array $f): array
    {
        $page = max(1, $page);
        $perPage = min(50, max(5, $perPage));

        $allowedEstado = ['S', 'C', 'A', 'CA'];
        $estadoF = isset($f['estado']) ? trim((string) $f['estado']) : '';
        $estadoSql = in_array($estadoF, $allowedEstado, true) ? $estadoF : null;

        $fechaDesde = gp_grid_date_opt(isset($f['fecha_desde']) ? (string) $f['fecha_desde'] : null);
        $fechaHasta = gp_grid_date_opt(isset($f['fecha_hasta']) ? (string) $f['fecha_hasta'] : null);
        $motivoLike = gp_grid_like_contains(gp_grid_str(isset($f['motivo']) ? (string) $f['motivo'] : null));
        $clienteLike = gp_grid_like_contains(gp_grid_str(isset($f['cliente']) ? (string) $f['cliente'] : null));
        $soloFuturas = isset($f['solo_futuras']) && (string) $f['solo_futuras'] === '1';

        $db = BasedeDatos::Conectar();
        $bind = [':fid' => $fisioId];
        $conds = ['c.fisio_id = :fid'];

        if ($estadoSql !== null) {
            $conds[] = 'c.estado = :est';
            $bind[':est'] = $estadoSql;
        }
        if ($fechaDesde !== null) {
            $conds[] = 'DATE(c.fecha) >= :fdesde';
            $bind[':fdesde'] = $fechaDesde;
        }
        if ($fechaHasta !== null) {
            $conds[] = 'DATE(c.fecha) <= :fhasta';
            $bind[':fhasta'] = $fechaHasta;
        }
        if ($motivoLike !== null) {
            $conds[] = 'IFNULL(c.motivo, \'\') LIKE :mot';
            $bind[':mot'] = $motivoLike;
        }
        if ($clienteLike !== null) {
            $conds[] = '(
                IFNULL(u.nombre, \'\') LIKE :cl
                OR IFNULL(u.apellido1, \'\') LIKE :cl
                OR IFNULL(u.apellido2, \'\') LIKE :cl
                OR IFNULL(u.email, \'\') LIKE :cl
                OR IFNULL(u.DNI, \'\') LIKE :cl
            )';
            $bind[':cl'] = $clienteLike;
        }
        if ($soloFuturas) {
            $conds[] = 'c.fecha >= NOW()';
        }

        $where = ' WHERE ' . implode(' AND ', $conds);

        $from = ' FROM citas c
            LEFT JOIN clientes cli ON cli.id = c.cliente_id
            LEFT JOIN usuarios u ON u.id = cli.usuario_id ';

        $st = $db->prepare('SELECT COUNT(DISTINCT c.id) ' . $from . $where);
        foreach ($bind as $k => $v) {
            $st->bindValue($k, $v);
        }
        $st->execute();
        $total = (int) $st->fetchColumn();

        $totalPages = $total > 0 ? max(1, (int) ceil($total / $perPage)) : 1;
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT c.id, c.fecha, c.motivo, c.estado, c.cliente_id,
                u.nombre AS cli_nombre, u.apellido1 AS cli_apellido1, u.apellido2 AS cli_apellido2,
                u.email AS cli_email, u.telefono AS cli_telefono, u.DNI AS cli_dni
            ' . $from . $where . '
            ORDER BY c.fecha DESC
            LIMIT :lim OFFSET :off';

        $st = $db->prepare($sql);
        foreach ($bind as $k => $v) {
            $st->bindValue($k, $v);
        }
        $st->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $st->bindValue(':off', $offset, PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        return [
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
        ];
    }
}
