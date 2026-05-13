<?php
require_once "app/modelos/usuario.php";

class Monitor extends Usuario
{
    private $especialidad;
    private $disponibilidad;

    public function __construct($DNI, $nombre, $apellido1, $apellido2, $email, $clave, $telefono, $especialidad, $disponibilidad)
    {
        parent::__construct($DNI, $nombre, $apellido1, $apellido2, $email, $clave, $telefono);
        $this->especialidad = $especialidad;
        $this->disponibilidad = $disponibilidad;
    }

    public function getEspecialidad()
    {
        return $this->especialidad;
    }

    public function getDisponibilidad()
    {
        return $this->disponibilidad;
    }

    public function guardar()
    {
        $conexion = BasedeDatos::Conectar();
        $stmt = $conexion->prepare("INSERT INTO monitores (usuario_id, especialidad, disponibilidad) VALUES (:usuario_id, :especialidad, :disponibilidad)");
        $stmt->bindValue(':usuario_id', parent::getId());
        $stmt->bindValue(':especialidad', $this->especialidad);
        $stmt->bindValue(':disponibilidad', $this->disponibilidad);
        return $stmt->execute();
    }

    /**
     * ID de la fila en `monitores` para un usuario de la aplicación (sesión).
     */
    public static function obtenerIdPorUsuarioId(int $usuarioId): ?int
    {
        $conexion = BasedeDatos::Conectar();
        $stmt = $conexion->prepare('SELECT id FROM monitores WHERE usuario_id = ? LIMIT 1');
        $stmt->execute([$usuarioId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int) $row['id'] : null;
    }

    /**
     * @param array{q?: string|null, dni?: string|null, email?: string|null, nombre?: string|null, especialidad?: string|null, disponibilidad?: string|null} $f
     *
     * @return array{rows: array<int, array<string,mixed>>, total: int, page: int, per_page: int, total_pages: int}
     */
    public static function buscarPaginado(int $page, int $perPage, array $f = []): array
    {
        $page = max(1, $page);
        $perPage = min(50, max(5, $perPage));

        $conexion = BasedeDatos::Conectar();
        $bind = [];
        $conds = [];

        $qLike = gp_grid_like_contains(gp_grid_str($f['q'] ?? null));
        if ($qLike !== null) {
            $conds[] = '(
                u.nombre LIKE :q OR u.apellido1 LIKE :q OR IFNULL(u.apellido2, \'\') LIKE :q
                OR u.email LIKE :q OR u.DNI LIKE :q OR IFNULL(u.telefono, \'\') LIKE :q
                OR IFNULL(m.especialidad, \'\') LIKE :q OR IFNULL(m.disponibilidad, \'\') LIKE :q)';
            $bind[':q'] = $qLike;
        }
        if (($dni = gp_grid_like_contains(gp_grid_str($f['dni'] ?? null))) !== null) {
            $conds[] = 'u.DNI LIKE :f_dni';
            $bind[':f_dni'] = $dni;
        }
        if (($mail = gp_grid_like_contains(gp_grid_str($f['email'] ?? null))) !== null) {
            $conds[] = 'u.email LIKE :f_mail';
            $bind[':f_mail'] = $mail;
        }
        if (($nom = gp_grid_like_contains(gp_grid_str($f['nombre'] ?? null))) !== null) {
            $conds[] = '(u.nombre LIKE :f_nom OR u.apellido1 LIKE :f_nom OR IFNULL(u.apellido2, \'\') LIKE :f_nom)';
            $bind[':f_nom'] = $nom;
        }
        if (($esp = gp_grid_like_contains(gp_grid_str($f['especialidad'] ?? null))) !== null) {
            $conds[] = 'IFNULL(m.especialidad, \'\') LIKE :f_esp';
            $bind[':f_esp'] = $esp;
        }
        if (($disp = gp_grid_like_contains(gp_grid_str($f['disponibilidad'] ?? null))) !== null) {
            $conds[] = 'IFNULL(m.disponibilidad, \'\') LIKE :f_disp';
            $bind[':f_disp'] = $disp;
        }

        $where = $conds !== [] ? ' WHERE ' . implode(' AND ', $conds) : '';
        $baseFrom = ' FROM monitores m INNER JOIN usuarios u ON m.usuario_id = u.id ';

        $stmt = $conexion->prepare('SELECT COUNT(*) ' . $baseFrom . $where);
        foreach ($bind as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $total = (int) $stmt->fetchColumn();

        $totalPages = $total > 0 ? max(1, (int) ceil($total / $perPage)) : 1;
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT m.id AS monitor_id, m.especialidad, m.disponibilidad, u.id AS usuario_id, u.DNI, u.nombre, u.apellido1, u.apellido2, u.email, u.telefono '
            . $baseFrom . $where
            . ' ORDER BY m.id DESC LIMIT :lim OFFSET :off';
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

    public static function obtenerTodos()
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->query("
            SELECT 
                m.id AS monitor_id,
                m.especialidad,
                m.disponibilidad,
                u.id AS usuario_id,
                u.DNI,
                u.nombre,
                u.apellido1,
                u.apellido2,
                u.email,
                u.telefono
            FROM monitores m
            JOIN usuarios u ON m.usuario_id = u.id
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId($id)
    {
        $conexion = BasedeDatos::Conectar();
        $stmt = $conexion->prepare("SELECT 
            m.id AS monitor_id,
            m.especialidad,
            m.disponibilidad,
            u.id AS usuario_id,
            u.DNI,
            u.nombre,
            u.apellido1,
            u.apellido2,
            u.email,
            u.telefono
        FROM monitores m
        JOIN usuarios u ON m.usuario_id = u.id WHERE m.id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function eliminar($id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("SELECT usuario_id FROM monitores WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $monitor = $stmt->fetch();

        if (!$monitor) {
            return false;
        }

        $usuario_id = $monitor['usuario_id'];

        $stmt = $conexion->prepare("DELETE FROM monitores WHERE id = :id");
        $stmt->bindValue(':id', $id);
        if ($stmt->execute()) {
            $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = :usuario_id");
            $stmt->bindValue(':usuario_id', $usuario_id);
            return $stmt->execute();
        }

        return false;
    }

    public static function actualizar($id, $DNI, $nombre, $apellido1, $apellido2, $email, $clave, $telefono, $especialidad = null, $disponibilidad = null)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("SELECT usuario_id FROM monitores WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $monitor = $stmt->fetch();

        if (!$monitor) {
            return false;
        }

        $usuario_id = $monitor['usuario_id'];

        if ($clave) {
            $stmt = $conexion->prepare("UPDATE usuarios SET DNI = :DNI, nombre = :nombre, apellido1 = :apellido1, apellido2 = :apellido2, email = :email, clave = :clave, telefono = :telefono, password_changed_at = NOW() WHERE id = :usuario_id");
            $stmt->bindValue(':clave', password_hash($clave, PASSWORD_DEFAULT));
        } else {
            $stmt = $conexion->prepare("UPDATE usuarios SET DNI = :DNI, nombre = :nombre, apellido1 = :apellido1, apellido2 = :apellido2, email = :email, telefono = :telefono WHERE id = :usuario_id");
        }

        $stmt->bindValue(':DNI', $DNI);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':apellido1', $apellido1);
        $stmt->bindValue(':apellido2', $apellido2);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':telefono', $telefono);
        $stmt->bindValue(':usuario_id', $usuario_id);

        if ($stmt->execute()) {
            $stmt = $conexion->prepare("UPDATE monitores SET especialidad = :especialidad, disponibilidad = :disponibilidad WHERE id = :id");
            $stmt->bindValue(':especialidad', $especialidad);
            $stmt->bindValue(':disponibilidad', $disponibilidad);
            $stmt->bindValue(':id', $id);
            return $stmt->execute();
        }

        return false;
    }
}
