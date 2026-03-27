<?php

class Usuario
{
    private $id;
    private $DNI;
    private $nombre;
    private $apellido1;
    private $apellido2;
    private $email;
    private $clave;
    private $telefono;

    public function __construct($DNI, $nombre, $apellido1, $apellido2, $email, $clave, $telefono)
    {
        $this->DNI = $DNI;
        $this->nombre = $nombre;
        $this->apellido1 = $apellido1;
        $this->apellido2 = $apellido2;
        $this->email = $email;
        $this->clave = $clave;
        $this->telefono = $telefono;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDNI()
    {
        return $this->DNI;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getApellido1()
    {
        return $this->apellido1;
    }

    public function getApellido2()
    {
        return $this->apellido2;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getClave()
    {
        return $this->clave;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function registrar()
    {
        $conexion = BasedeDatos::Conectar();
        if ($conexion) {
            try {
                $hashedPassword = password_hash($this->clave, PASSWORD_DEFAULT);
                $stmt = $conexion->prepare("INSERT INTO usuarios (DNI, nombre, apellido1, apellido2, email, clave, telefono) VALUES (:DNI, :nombre, :apellido1, :apellido2, :email, :clave, :telefono)");
                $stmt->bindParam(':DNI', $this->DNI);
                $stmt->bindParam(':nombre', $this->nombre);
                $stmt->bindParam(':apellido1', $this->apellido1);
                $stmt->bindParam(':apellido2', $this->apellido2);
                $stmt->bindParam(':email', $this->email);
                $stmt->bindParam(':clave', $hashedPassword);
                $stmt->bindParam(':telefono', $this->telefono);
                return $stmt->execute();
            } catch (\Throwable $th) {
                echo "Error al registrar el usuario: " . $th->getMessage();
                return false;
            }
        } else {
            return false;
        }
    }
    public static function obtenerPorId($id)
    {
        $conexion = BasedeDatos::Conectar();
        if ($conexion) {
            try {
                $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                if ($stmt->rowCount() == 1) {
                    $usuarioData = $stmt->fetch(PDO::FETCH_ASSOC);
                    return new Usuario(
                        $usuarioData['DNI'],
                        $usuarioData['nombre'],
                        $usuarioData['apellido1'],
                        $usuarioData['apellido2'],
                        $usuarioData['email'],
                        $usuarioData['clave'],
                        $usuarioData['telefono']
                    );
                }
            } catch (\Throwable $th) {
                echo "Error al obtener el usuario: " . $th->getMessage();
                return null;
            }
        } else {
            return null;
        }
    }
}

?>