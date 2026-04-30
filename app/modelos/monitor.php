<?php
require_once "app/modelos/usuario.php";

class Monitor extends Usuario
{
    private $especialidad;
    private $disponibilidad;

    public function __construct($nombre, $email, $clave, $telefono, $especialidad, $disponibilidad)
    {
        parent::__construct($nombre, $email, $clave, $telefono);
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

        // Obtener monitor
        $stmt = $conexion->prepare("SELECT usuario_id FROM monitores WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $monitor = $stmt->fetch();

        if (!$monitor) {
            return false;
        }

        $usuario_id = $monitor['usuario_id'];

        // Eliminar monitor
        $stmt = $conexion->prepare("DELETE FROM monitores WHERE id = :id");
        $stmt->bindValue(':id', $id);
        if ($stmt->execute()) {
            // Eliminar usuario asociado
            $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = :usuario_id");
            $stmt->bindValue(':usuario_id', $usuario_id);
            return $stmt->execute();
        }

        return false;
    }

    public static function actualizar($id, $DNI, $nombre, $apellido1, $apellido2, $email, $clave, $telefono, $especialidad, $disponibilidad)
    {
        $conexion = BasedeDatos::Conectar();

        // Obtener monitor
        $stmt = $conexion->prepare("SELECT usuario_id FROM monitores WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $monitor = $stmt->fetch();

        if (!$monitor) {
            return false;
        }

        $usuario_id = $monitor['usuario_id'];

        // Actualizar usuario
        if ($clave) {
            // Si se proporciona una nueva contraseña
            $stmt = $conexion->prepare("UPDATE usuarios SET DNI = :DNI, nombre = :nombre, apellido1 = :apellido1, apellido2 = :apellido2, email = :email, clave = :clave, telefono = :telefono WHERE id = :usuario_id");
            $stmt->bindValue(':clave', password_hash($clave, PASSWORD_DEFAULT));
        } else {
            // Si no se cambia la contraseña
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
            // Actualizar monitor
            $stmt = $conexion->prepare("UPDATE monitores SET especialidad = :especialidad, disponibilidad = :disponibilidad WHERE id = :id");
            $stmt->bindValue(':especialidad', $especialidad);
            $stmt->bindValue(':disponibilidad', $disponibilidad);
            $stmt->bindValue(':id', $id);
            return $stmt->execute();
        }

        return false;
    }
}
