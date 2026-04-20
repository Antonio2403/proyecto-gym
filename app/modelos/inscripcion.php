<?php

class Inscripcion
{
    public static function yaInscrito($cliente_id, $actividad_id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            SELECT COUNT(*) FROM inscripciones 
            WHERE cliente_id = :cliente_id AND actividad_id = :actividad_id
        ");

        $stmt->bindValue(':cliente_id', $cliente_id);
        $stmt->bindValue(':actividad_id', $actividad_id);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public static function contarInscritos($actividad_id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            SELECT COUNT(*) FROM inscripciones 
            WHERE actividad_id = :actividad_id
        ");

        $stmt->bindValue(':actividad_id', $actividad_id);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public static function inscribir($cliente_id, $actividad_id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
                INSERT INTO inscripciones (cliente_id, actividad_id) 
                VALUES (:cliente_id, :actividad_id)
            ");

        $stmt->bindValue(':cliente_id', $cliente_id);
        $stmt->bindValue(':actividad_id', $actividad_id);

        return $stmt->execute();
    }

    public static function obtenerInscripciones()
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
        SELECT 
            i.id,
            a.id AS actividad_id,
            a.nombre AS actividad,
            a.descripcion,
            a.fecha_inicio,
            a.fecha_fin,
            a.dia_semana,
            s.nombre AS sala,
            u.nombre AS monitor

        FROM inscripciones i

        INNER JOIN clientes c ON i.cliente_id = c.id
        INNER JOIN usuarios uc ON c.usuario_id = uc.id

        INNER JOIN actividades a ON i.actividad_id = a.id

        LEFT JOIN salas s ON a.sala_id = s.id
        LEFT JOIN monitores m ON a.monitor_id = m.id
        LEFT JOIN usuarios u ON m.usuario_id = u.id

        WHERE c.usuario_id = :usuario_id

        ORDER BY a.dia_semana, a.fecha_inicio
    ");

        $stmt->bindValue(':usuario_id', $_SESSION['usuario_id']);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function cancelar($id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            DELETE FROM inscripciones WHERE id = :id
        ");

        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}
