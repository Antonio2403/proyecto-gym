<?php

require_once __DIR__ . '/actividades.php';

class Sala
{
    public static function obtenerTodas()
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->query("SELECT * FROM salas");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId($id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("SELECT * FROM salas WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function crear($nombre, $capacidad, $disponibilidad, string $color = '#C45C26')
    {
        $conexion = BasedeDatos::Conectar();
        $colorOk = Actividad::normalizarColor($color) ?? '#C45C26';

        $stmt = $conexion->prepare("
            INSERT INTO salas (nombre, color, capacidad, disponibilidad) VALUES (:nombre, :color, :capacidad, :disponibilidad)
        ");

        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':color', $colorOk);
        $stmt->bindValue(':capacidad', $capacidad);
        $stmt->bindValue(':disponibilidad', $disponibilidad);
        return $stmt->execute();
    }

    public static function eliminar($id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("DELETE FROM salas WHERE id = :id");
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    public static function actualizar($id, $nombre, $capacidad, $disponibilidad, string $color = '#C45C26')
    {
        $conexion = BasedeDatos::Conectar();
        $colorOk = Actividad::normalizarColor($color) ?? '#C45C26';

        $stmt = $conexion->prepare("
            UPDATE salas SET nombre = :nombre, color = :color, capacidad = :capacidad, disponibilidad = :disponibilidad WHERE id = :id
        ");

        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':color', $colorOk);
        $stmt->bindValue(':capacidad', $capacidad);
        $stmt->bindValue(':disponibilidad', $disponibilidad);
        return $stmt->execute();
    }
}