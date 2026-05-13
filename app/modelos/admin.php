<?php
require_once "app/modelos/usuario.php";

class Admin extends Usuario
{
    public function __construct($DNI, $nombre, $apellido1, $apellido2, $email, $clave, $telefono)
    {
        parent::__construct($DNI, $nombre, $apellido1, $apellido2, $email, $clave, $telefono);
    }

    public static function crearMonitor($datos)
    {
        $conexion = BasedeDatos::Conectar();

        try {
            $conexion->beginTransaction();

            $hashedPassword = password_hash($datos['clave'], PASSWORD_DEFAULT);

            $stmt = $conexion->prepare("
            INSERT INTO usuarios (DNI, nombre, apellido1, apellido2, email, clave, telefono, password_changed_at)
            VALUES (:DNI, :nombre, :apellido1, :apellido2, :email, :clave, :telefono, NOW())
        ");

            $stmt->execute([
                ':DNI' => $datos['DNI'],
                ':nombre' => $datos['nombre'],
                ':apellido1' => $datos['apellido1'],
                ':apellido2' => $datos['apellido2'],
                ':email' => $datos['email'],
                ':clave' => $hashedPassword,
                ':telefono' => $datos['telefono']
            ]);

            $usuario_id = $conexion->lastInsertId();

            $stmt = $conexion->prepare("
            INSERT INTO monitores (usuario_id, especialidad, disponibilidad)
            VALUES (:usuario_id, :especialidad, :disponibilidad)
        ");

            $stmt->execute([
                ':usuario_id' => $usuario_id,
                ':especialidad' => $datos['especialidad'],
                ':disponibilidad' => $datos['disponibilidad']
            ]);

            $conexion->commit();

            return true;
        } catch (Throwable $th) {
            $conexion->rollBack();
            error_log('[Admin] crearMonitor: ' . $th->getMessage());
            return false;
        }
    }
}
