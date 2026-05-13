<?php

class Subscripcion
{
    public static function crear($nombre, $precio, $duracion, int $numeroClases = 0, string $fisio = 'N', int $enOferta = 0, ?string $ofertaMotivo = null, ?string $ofertaFin = null)
    {
        $db = BasedeDatos::Conectar();
        $query = "INSERT INTO subscripciones (nombre, precio, duracion, numero_clases, fisio, en_oferta, oferta_motivo, oferta_fin, estado) VALUES (:nombre, :precio, :duracion, :numero_clases, :fisio, :en_oferta, :oferta_motivo, :oferta_fin, 'A')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':duracion', $duracion);
        $stmt->bindValue(':numero_clases', $numeroClases, PDO::PARAM_INT);
        $stmt->bindValue(':fisio', $fisio);
        $stmt->bindValue(':en_oferta', $enOferta, PDO::PARAM_INT);
        $stmt->bindValue(':oferta_motivo', $ofertaMotivo);
        $stmt->bindValue(':oferta_fin', $ofertaFin);
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
        self::sincronizarCatalogo($db);
        $stmt = $db->query("SELECT * FROM subscripciones WHERE estado = 'A' AND (en_oferta = 0 OR (oferta_fin IS NOT NULL AND oferta_fin > NOW())) ORDER BY precio ASC, id ASC");

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
        self::sincronizarCatalogo($db);
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
        self::sincronizarCatalogo($db);
        $query = "SELECT * FROM subscripciones WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function eliminar($id)
    {
        $db = BasedeDatos::Conectar();
        $query = "UPDATE subscripciones SET estado = 'I' WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $ok = $stmt->execute();
        self::purgarRetiradasSinUso($db);

        return $ok;
    }
    public static function actualizar($id, $nombre, $precio, $duracion, int $numeroClases = 0, string $fisio = 'N', int $enOferta = 0, ?string $ofertaMotivo = null, ?string $ofertaFin = null)
    {
        $db = BasedeDatos::Conectar();
        $query = "UPDATE subscripciones SET nombre = :nombre, precio = :precio, duracion = :duracion, numero_clases = :numero_clases, fisio = :fisio, en_oferta = :en_oferta, oferta_motivo = :oferta_motivo, oferta_fin = :oferta_fin WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':duracion', $duracion);
        $stmt->bindValue(':numero_clases', $numeroClases, PDO::PARAM_INT);
        $stmt->bindValue(':fisio', $fisio);
        $stmt->bindValue(':en_oferta', $enOferta, PDO::PARAM_INT);
        $stmt->bindValue(':oferta_motivo', $ofertaMotivo);
        $stmt->bindValue(':oferta_fin', $ofertaFin);
        return $stmt->execute();
    }

    public static function sincronizarCatalogo(?PDO $db = null): void
    {
        $db = $db ?: BasedeDatos::Conectar();
        self::cerrarSuscripcionesClienteCaducadas($db);
        self::retirarOfertasFueraDePlazo($db);
        self::purgarRetiradasSinUso($db);
    }

    /**
     * El plazo de oferta solo limita la compra. No toca cliente_subscripcion:
     * quien compró antes conserva su fecha_fin.
     */
    private static function retirarOfertasFueraDePlazo(PDO $db): void
    {
        try {
            $db->exec("UPDATE subscripciones SET estado = 'I' WHERE estado = 'A' AND en_oferta = 1 AND (oferta_fin IS NULL OR oferta_fin <= NOW())");
        } catch (Throwable $e) {
            error_log('[Subscripcion] retirarOfertasFueraDePlazo: ' . $e->getMessage());
        }
    }

    private static function cerrarSuscripcionesClienteCaducadas(PDO $db): void
    {
        try {
            $db->exec("UPDATE cliente_subscripcion SET estado = 'C' WHERE estado = 'A' AND fecha_fin IS NOT NULL AND fecha_fin < NOW()");
        } catch (Throwable $e) {
            error_log('[Subscripcion] cerrarSuscripcionesClienteCaducadas: ' . $e->getMessage());
        }
    }

    /**
     * Borra del catálogo las suscripciones retiradas cuando ningún cliente la usa ya.
     */
    public static function purgarRetiradasSinUso(?PDO $db = null): void
    {
        $db = $db ?: BasedeDatos::Conectar();

        try {
            $st = $db->query(
                "SELECT s.id
                 FROM subscripciones s
                 WHERE s.estado = 'I'
                   AND NOT EXISTS (
                       SELECT 1
                       FROM cliente_subscripcion cs
                       WHERE cs.subscripcion_id = s.id
                         AND cs.estado = 'A'
                         AND (cs.fecha_fin IS NULL OR cs.fecha_fin >= NOW())
                   )"
            );
            $ids = $st ? array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN)) : [];
            if ($ids === []) {
                return;
            }

            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $upd = $db->prepare(
                "UPDATE cliente_subscripcion
                 SET subscripcion_id = NULL
                 WHERE subscripcion_id IN ($placeholders)
                   AND (estado <> 'A' OR fecha_fin IS NULL OR fecha_fin < NOW())"
            );
            $upd->execute($ids);

            $del = $db->prepare("DELETE FROM subscripciones WHERE id IN ($placeholders)");
            $del->execute($ids);
        } catch (Throwable $e) {
            error_log('[Subscripcion] purgarRetiradasSinUso: ' . $e->getMessage());
        }
    }
}
