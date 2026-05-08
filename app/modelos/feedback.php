<?php

class Feedback
{
    public static function crear(string $nombre, string $email, string $asunto, string $mensaje): bool
    {
        $db = BasedeDatos::Conectar();
        $sql = 'INSERT INTO feedback (nombre, email, asunto, mensaje) VALUES (?, ?, ?, ?)';
        $st = $db->prepare($sql);

        return (bool) $st->execute([$nombre, $email, $asunto, $mensaje]);
    }

    public static function obtenerTodos(): array
    {
        $db = BasedeDatos::Conectar();
        $sql = 'SELECT id, nombre, email, asunto, mensaje, fecha_creacion
                FROM feedback
                ORDER BY fecha_creacion DESC';
        $st = $db->query($sql);

        return $st ? $st->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * @return array{rows: array<int, array<string,mixed>>, total: int, page: int, per_page: int, total_pages: int}
     */
    public static function buscarPaginado(int $page, int $perPage, array $f): array
    {
        $page = max(1, $page);
        $perPage = min(50, max(5, $perPage));

        $db = BasedeDatos::Conectar();
        $bind = [];
        $conds = [];

        $qLike = gp_grid_like_contains(gp_grid_str($f['q'] ?? null));
        if ($qLike !== null) {
            $conds[] = '(nombre LIKE :q OR email LIKE :q OR asunto LIKE :q OR mensaje LIKE :q)';
            $bind[':q'] = $qLike;
        }
        if (($nom = gp_grid_like_contains(gp_grid_str($f['nombre'] ?? null))) !== null) {
            $conds[] = 'nombre LIKE :fn';
            $bind[':fn'] = $nom;
        }
        if (($mail = gp_grid_like_contains(gp_grid_str($f['email'] ?? null))) !== null) {
            $conds[] = 'email LIKE :em';
            $bind[':em'] = $mail;
        }
        if (($as = gp_grid_like_contains(gp_grid_str($f['asunto'] ?? null))) !== null) {
            $conds[] = 'asunto LIKE :as';
            $bind[':as'] = $as;
        }
        $fd = gp_grid_date_opt($f['fecha_desde'] ?? null);
        if ($fd !== null) {
            $conds[] = 'DATE(fecha_creacion) >= :fd';
            $bind[':fd'] = $fd;
        }
        $fh = gp_grid_date_opt($f['fecha_hasta'] ?? null);
        if ($fh !== null) {
            $conds[] = 'DATE(fecha_creacion) <= :fh';
            $bind[':fh'] = $fh;
        }

        $where = $conds !== [] ? ' WHERE ' . implode(' AND ', $conds) : '';

        $st = $db->prepare('SELECT COUNT(*) FROM feedback' . $where);
        foreach ($bind as $k => $v) {
            $st->bindValue($k, $v);
        }
        $st->execute();
        $total = (int) $st->fetchColumn();

        $totalPages = $total > 0 ? max(1, (int) ceil($total / $perPage)) : 1;
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT id, nombre, email, asunto, mensaje, fecha_creacion FROM feedback' . $where
            . ' ORDER BY fecha_creacion DESC LIMIT :lim OFFSET :off';
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

    public static function eliminar(int $id): bool
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare('DELETE FROM feedback WHERE id = ?');

        return (bool) $st->execute([$id]);
    }
}
