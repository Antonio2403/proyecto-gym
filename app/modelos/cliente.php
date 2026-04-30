<?php

require_once "app/modelos/usuario.php";

class Cliente extends Usuario
{
    private $metodoPago;

    public function __construct($DNI, $nombre, $apellido1, $apellido2, $email, $clave, $telefono, $metodoPago)
    {
        parent::__construct($DNI, $nombre, $apellido1, $apellido2, $email, $clave, $telefono);
        $this->metodoPago = $metodoPago;
    }

    public function getMetodoPago()
    {
        return $this->metodoPago;
    }

    public function registrar()
    {
        $conexion = BasedeDatos::Conectar();
        $stmt = $conexion->prepare("INSERT INTO clientes (usuario_id, metodo_pago) VALUES (:usuario_id, :metodo_pago)");
        $stmt->bindValue(':usuario_id', parent::getId());
        $stmt->bindValue(':metodo_pago', $this->metodoPago);
        return $stmt->execute();
    }

    public static function obtenerTodos()
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->query("
            SELECT 
                c.id AS cliente_id,
                c.metodo_pago,
                u.id AS usuario_id,
                u.DNI,
                u.nombre,
                u.apellido1,
                u.apellido2,
                u.email,
                u.telefono
            FROM clientes c
            JOIN usuarios u ON c.usuario_id = u.id
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId($id)
    {
        $conexion = BasedeDatos::Conectar();

        $stmt = $conexion->prepare("
            SELECT 
                c.id AS cliente_id,
                c.metodo_pago,
                u.id AS usuario_id,
                u.DNI,
                u.nombre,
                u.apellido1,
                u.apellido2,
                u.email,
                u.telefono
            FROM clientes c
            JOIN usuarios u ON c.usuario_id = u.id
            WHERE c.id = :id
        ");

        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function IdClientePorUsuarioId($usuario_id)
{
    $conexion = BasedeDatos::Conectar();

    $stmt = $conexion->prepare("
        SELECT id 
        FROM clientes 
        WHERE usuario_id = :usuario_id
    ");

    $stmt->bindValue(':usuario_id', $usuario_id);
    $stmt->execute();

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    return $resultado ? $resultado['id'] : null;
}




}