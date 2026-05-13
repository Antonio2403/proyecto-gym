<?php

require_once "app/modelos/usuario.php";

class Cliente extends Usuario
{
    private $metodoPago;

    public function __construct($DNI, $nombre, $apellido1, $apellido2, $email, $clave, $telefono, $metodoPago)
    {
        parent::__construct($DNI, $nombre, $apellido1, $apellido2, $email, $clave, $telefono);
        $this->metodoPago = $metodoPago;
    }

    public function getMetodoPago()
    {
        return $this->metodoPago;
    }

    public function registrar()
    {
        $conexion = BasedeDatos::Conectar();
        $stmt = $conexion->prepare("INSERT INTO clientes (usuario_id, metodo_pago) VALUES (:usuario_id, :metodo_pago)");
        $stmt->bindValue(':usuario_id', parent::getId());
        $stmt->bindValue(':metodo_pago', $this->metodoPago);
        return $stmt->execute();
    }

    public static function obtenerTodos()
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->query("
            SELECT 
                c.id AS cliente_id,
                c.metodo_pago,
                u.id AS usuario_id,
                u.DNI,
                u.nombre,
                u.apellido1,
                u.apellido2,
                u.email,
                u.telefono
            FROM clientes c
            JOIN usuarios u ON c.usuario_id = u.id
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista paginada con filtros (AND). `q` hace LIKE amplio sobre varios campos.
     *
     * @return array{rows: array<int, array<string,mixed>>, total: int, page: int, per_page: int, total_pages: int}
     */
    public static function buscarPaginado(int $page, int $perPage, array $f): array
    {
        $page = max(1, $page);
        $perPage = min(50, max(5, $perPage));

        $conexion = BasedeDatos::Conectar();
        $bind = [];
        $conds = [];

        $qLike = gp_grid_like_contains(gp_grid_str($f['q'] ?? null));
        if ($qLike !== null) {
            $conds[] = '(u.DNI LIKE :q OR u.nombre LIKE :q OR u.apellido1 LIKE :q OR IFNULL(u.apellido2, \'\') LIKE :q OR u.email LIKE :q OR IFNULL(u.telefono, \'\') LIKE :q OR IFNULL(c.metodo_pago, \'\') LIKE :q)';
            $bind[':q'] = $qLike;
        }

        if (($dni = gp_grid_like_contains(gp_grid_str($f['dni'] ?? null))) !== null) {
            $conds[] = 'u.DNI LIKE :f_dni';
            $bind[':f_dni'] = $dni;
        }
        if (($nom = gp_grid_like_contains(gp_grid_str($f['nombre'] ?? null))) !== null) {
            $conds[] = '(u.nombre LIKE :f_nom OR u.apellido1 LIKE :f_nom OR IFNULL(u.apellido2, \'\') LIKE :f_nom)';
            $bind[':f_nom'] = $nom;
        }
        if (($mail = gp_grid_like_contains(gp_grid_str($f['email'] ?? null))) !== null) {
            $conds[] = 'u.email LIKE :f_mail';
            $bind[':f_mail'] = $mail;
        }
        if (($tel = gp_grid_like_contains(gp_grid_str($f['telefono'] ?? null))) !== null) {
            $conds[] = 'IFNULL(u.telefono, \'\') LIKE :f_tel';
            $bind[':f_tel'] = $tel;
        }
        $mp = gp_grid_str($f['metodo_pago'] ?? null);
        if ($mp !== null) {
            $conds[] = 'IFNULL(c.metodo_pago, \'\') LIKE :f_mp';
            $bind[':f_mp'] = '%' . gp_grid_escape_like($mp) . '%';
        }

        $where = $conds !== [] ? ' WHERE ' . implode(' AND ', $conds) : '';
        $baseFrom = ' FROM clientes c INNER JOIN usuarios u ON c.usuario_id = u.id ';

        $sel = '
            SELECT
                c.id AS cliente_id,
                c.metodo_pago,
                u.id AS usuario_id,
                u.DNI,
                u.nombre,
                u.apellido1,
                u.apellido2,
                u.email,
                u.telefono,
                u.bloqueo_tipo,
                u.bloqueado_hasta,
                u.bloqueo_motivo,
                cs.id AS plan_rel_id,
                cs.fecha_fin AS plan_fecha_fin,
                s.nombre AS plan_nombre';

        $stmt = $conexion->prepare('SELECT COUNT(*) ' . $baseFrom . $where);
        foreach ($bind as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $total = (int) $stmt->fetchColumn();

        $totalPages = $total > 0 ? max(1, (int) ceil($total / $perPage)) : 1;
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = $sel . $baseFrom . "
            LEFT JOIN cliente_subscripcion cs
                ON cs.cliente_id = c.id
               AND cs.estado = 'A'
               AND (cs.fecha_inicio IS NULL OR cs.fecha_inicio <= NOW())
               AND (cs.fecha_fin IS NULL OR cs.fecha_fin >= NOW())
            LEFT JOIN subscripciones s ON s.id = cs.subscripcion_id
        " . $where . ' ORDER BY c.id DESC LIMIT :lim OFFSET :off';
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

    public static function cancelarPlanActivo(int $clienteId): bool
    {
        $conexion = BasedeDatos::Conectar();
        if (!$conexion) {
            return false;
        }

        try {
            $stmt = $conexion->prepare(
                "UPDATE cliente_subscripcion
                 SET estado = 'C', fecha_fin = LEAST(IFNULL(fecha_fin, NOW()), NOW())
                 WHERE cliente_id = ?
                   AND estado = 'A'
                   AND (fecha_inicio IS NULL OR fecha_inicio <= NOW())
                   AND (fecha_fin IS NULL OR fecha_fin >= NOW())"
            );
            $stmt->execute([$clienteId]);
            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            error_log('[Cliente] cancelarPlanActivo: ' . $e->getMessage());
            return false;
        }
    }

    public static function usuarioIdPorClienteId(int $clienteId): ?int
    {
        $conexion = BasedeDatos::Conectar();
        if (!$conexion) {
            return null;
        }

        $stmt = $conexion->prepare('SELECT usuario_id FROM clientes WHERE id = ? LIMIT 1');
        $stmt->execute([$clienteId]);
        $id = $stmt->fetchColumn();

        return $id !== false ? (int) $id : null;
    }

    public static function obtenerPorId($id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare('
            SELECT
                c.id AS cliente_id,
                c.metodo_pago,
                u.id AS usuario_id,
                u.DNI,
                u.nombre,
                u.apellido1,
                u.apellido2,
                u.email,
                u.telefono
            FROM clientes c
            JOIN usuarios u ON c.usuario_id = u.id
            WHERE c.id = :id
        ');

        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function IdClientePorUsuarioId($usuario_id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare('
            SELECT id
            FROM clientes
            WHERE usuario_id = :usuario_id
        ');

        $stmt->bindValue(':usuario_id', $usuario_id);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ? $resultado['id'] : null;
    }
}