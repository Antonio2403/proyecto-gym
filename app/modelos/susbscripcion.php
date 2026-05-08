<?php

class Subscripcion
{
    public static function crear($nombre, $precio, $duracion)
    {
        $db = BasedeDatos::Conectar();
        $query = "INSERT INTO subscripciones (nombre, precio, duracion) VALUES (:nombre, :precio, :duracion)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':duracion', $duracion);
        return $stmt->execute();
    }
    public static function obtenerTodas()
    {
        $db = BasedeDatos::Conectar();
        $query = "SELECT * FROM subscripciones";
        $stmt = $db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Planes disponibles para contratar en la web (catálogo activo). */
    public static function obtenerActivasCatalogo(): array
    {
        $db = BasedeDatos::Conectar();
        $stmt = $db->query("SELECT * FROM subscripciones WHERE estado = 'A' ORDER BY precio ASC, id ASC");

        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
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
            $conds[] = 'nombre LIKE :q';
            $bind[':q'] = $qLike;
        }
        if (($nom = gp_grid_like_contains(gp_grid_str($f['nombre'] ?? null))) !== null) {
            $conds[] = 'nombre LIKE :f_nom';
            $bind[':f_nom'] = $nom;
        }
        $pmin = gp_grid_float_opt($f['precio_min'] ?? null);
        if ($pmin !== null) {
            $conds[] = 'precio >= :pmin';
            $bind[':pmin'] = $pmin;
        }
        $pmax = gp_grid_float_opt($f['precio_max'] ?? null);
        if ($pmax !== null) {
            $conds[] = 'precio <= :pmax';
            $bind[':pmax'] = $pmax;
        }
        $dmin = gp_grid_int_opt($f['duracion_min'] ?? null);
        if ($dmin !== null && $dmin > 0) {
            $conds[] = 'duracion >= :dmin';
            $bind[':dmin'] = $dmin;
        }
        $dmax = gp_grid_int_opt($f['duracion_max'] ?? null);
        if ($dmax !== null && $dmax > 0) {
            $conds[] = 'duracion <= :dmax';
            $bind[':dmax'] = $dmax;
        }

        $where = $conds !== [] ? ' WHERE ' . implode(' AND ', $conds) : '';
        $from = ' FROM subscripciones ';

        $stmt = $db->prepare('SELECT COUNT(*) ' . $from . $where);
        foreach ($bind as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $total = (int) $stmt->fetchColumn();

        $totalPages = $total > 0 ? max(1, (int) ceil($total / $perPage)) : 1;
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT *' . $from . $where . ' ORDER BY id DESC LIMIT :lim OFFSET :off';
        $stmt = $db->prepare($sql);
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

    public static function obtenerPorId($id)
    {
        $db = BasedeDatos::Conectar();
        $query = "SELECT * FROM subscripciones WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function eliminar($id)
    {
        $db = BasedeDatos::Conectar();
        $query = "DELETE FROM subscripciones WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    public static function actualizar($id, $nombre, $precio, $duracion)
    {
        $db = BasedeDatos::Conectar();
        $query = "UPDATE subscripciones SET nombre = :nombre, precio = :precio, duracion = :duracion WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':duracion', $duracion);
        return $stmt->execute();
    }
}
