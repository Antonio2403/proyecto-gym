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
}
