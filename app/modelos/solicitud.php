<?php

class Solicitud
{
    public static function crear($monitor_id, $tipo)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            INSERT INTO solicitudes (monitor_id, tipo)
            VALUES (:monitor_id, :tipo)
        ");

        return $stmt->execute([
            ':monitor_id' => $monitor_id,
            ':tipo' => $tipo
        ]);
    }

    public static function obtenerPendientes()
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->query("
            SELECT s.*, u.nombre FROM solicitudes s
            INNER JOIN usuarios u ON s.monitor_id = u.id
            WHERE s.estado = 'P'
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerAprobadas()
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->query("
            SELECT s.*, u.nombre FROM solicitudes s
            INNER JOIN usuarios u ON s.monitor_id = u.id
            WHERE s.estado = 'A'
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerRechazadas()
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->query("
            SELECT s.*, u.nombre FROM solicitudes s
            INNER JOIN usuarios u ON s.monitor_id = u.id
            WHERE s.estado = 'R'
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorMonitor($monitor_id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            SELECT * FROM solicitudes WHERE monitor_id = :monitor_id
        ");

        $stmt->execute([':monitor_id' => $monitor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function cambiarEstado($id, $estado, $admin_id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            UPDATE solicitudes 
            SET estado = :estado, admin_id = :admin_id 
            WHERE id = :id
        ");

        return $stmt->execute([
            ':estado' => $estado,
            ':admin_id' => $admin_id,
            ':id' => $id
        ]);
    }
}
