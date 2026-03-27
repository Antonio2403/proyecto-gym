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


?>