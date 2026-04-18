<?php

class Actividad
{
    public static function guardar($nombre, $descripcion, $sala_id, $monitor_id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            INSERT INTO actividades (nombre, descripcion, sala_id, monitor_id)
            VALUES (:nombre, :descripcion, :sala_id, :monitor_id)
        ");

        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':descripcion', $descripcion);
        $stmt->bindValue(':sala_id', $sala_id);
        $stmt->bindValue(':monitor_id', $monitor_id);

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

    public static function actualizar($id, $nombre, $descripcion)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            UPDATE actividades SET nombre = :nombre, descripcion = :descripcion WHERE id = :id
        ");

        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':descripcion', $descripcion);

        return $stmt->execute();
    }

    public static function obtenerTodas()
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->query("SELECT * FROM actividades");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function eliminar($id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("DELETE FROM actividades WHERE id = :id");
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }
}

?>