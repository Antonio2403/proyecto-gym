<?php
require_once "app/modelos/usuario.php";

class Monitor extends Usuario
{
    private $especialidad;
    private $disponibilidad;

    public function __construct($nombre, $email, $clave, $telefono, $especialidad, $disponibilidad)
    {
        parent::__construct($nombre, $email, $clave, $telefono);
        $this->especialidad = $especialidad;
        $this->disponibilidad = $disponibilidad;
    }

    public function getEspecialidad()
    {
        return $this->especialidad;
    }

    public function getDisponibilidad()
    {
        return $this->disponibilidad;
    }

    public function guardar()
    {
        $conexion = BasedeDatos::Conectar();
        $stmt = $conexion->prepare("INSERT INTO monitores (usuario_id, especialidad, disponibilidad) VALUES (:usuario_id, :especialidad, :disponibilidad)");
        $stmt->bindValue(':usuario_id', parent::getId());
        $stmt->bindValue(':especialidad', $this->especialidad);
        $stmt->bindValue(':disponibilidad', $this->disponibilidad);
        return $stmt->execute();
    }
}
