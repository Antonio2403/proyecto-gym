<?php

class Material
{


    public static function guardar($sala_id, $nombre, $estado)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            INSERT INTO materiales (sala_id, nombre, estado)
            VALUES (:sala_id, :nombre, :estado)
        ");

        $stmt->bindValue(':sala_id', $sala_id);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':estado', $estado);

        return $stmt->execute();
    }

    public static function obtenerPorSala($sala_id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            SELECT * FROM materiales WHERE sala_id = :sala_id
        ");

        $stmt->bindValue(':sala_id', $sala_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId($id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            SELECT * FROM materiales WHERE id = :id
        ");

        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function actualizar($id, $nombre, $estado)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            UPDATE materiales SET nombre = :nombre, estado = :estado WHERE id = :id
        ");

        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':estado', $estado);

        return $stmt->execute();
    }

    public static function eliminar($id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            DELETE FROM materiales WHERE id = :id
        ");

        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}


?>