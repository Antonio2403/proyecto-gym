<?php

class Sala
{
    public static function obtenerTodas()
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->query("SELECT * FROM salas");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crear($nombre, $capacidad , $disponibilidad)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            INSERT INTO salas (nombre, capacidad, disponibilidad) VALUES (:nombre, :capacidad, :disponibilidad)
        ");

        $stmt->bindValue(':nombre', $nombre);
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

    public static function actualizar($id, $nombre, $capacidad , $disponibilidad)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            UPDATE salas SET nombre = :nombre, capacidad = :capacidad, disponibilidad = :disponibilidad WHERE id = :id
        ");

        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':capacidad', $capacidad);
        $stmt->bindValue(':disponibilidad', $disponibilidad);
        return $stmt->execute();
    }
}