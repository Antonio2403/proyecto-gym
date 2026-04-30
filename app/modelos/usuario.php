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
                if ($stmt->execute()) {
                    $usuario_id = $conexion->lastInsertId();

                    $conexion->exec("
                        INSERT INTO clientes (usuario_id, metodo_pago)
                        VALUES ($usuario_id, 'desconocido')
                    ");
                    return true;
                }
                return false;
            } catch (\Throwable $th) {
                //echo "Error al registrar el usuario: " . $th->getMessage();
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

    public static function obtenerPorEmail($email)
    {
        $conexion = BasedeDatos::Conectar();

        if ($conexion) {

            try {

                $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                if ($stmt->rowCount() == 1) {

                    $usuarioData = $stmt->fetch(PDO::FETCH_ASSOC);

                    $usuario = new Usuario(
                        $usuarioData['DNI'],
                        $usuarioData['nombre'],
                        $usuarioData['apellido1'],
                        $usuarioData['apellido2'],
                        $usuarioData['email'],
                        $usuarioData['clave'],
                        $usuarioData['telefono']
                    );

                    $usuario->id = $usuarioData['id'];

                    return $usuario;
                }
            } catch (Throwable $th) {

                echo "Error al obtener usuario: " . $th->getMessage();
            }
        }

        return null;
    }

    public static function actualizar($id, $DNI, $nombre, $apellido1, $apellido2, $email, $clave, $telefono)
    {
        $conexion = BasedeDatos::Conectar();

        if (!$conexion) {
            return false;
        }

        try {

            // QUERY BASE (sin contraseña)
            $sql = "UPDATE usuarios 
                SET DNI = :DNI,
                    nombre = :nombre,
                    apellido1 = :apellido1,
                    apellido2 = :apellido2,
                    email = :email,
                    telefono = :telefono";

            // SI HAY CONTRASEÑA → se añade
            if (!empty($clave)) {
                $sql .= ", clave = :clave";
            }

            $sql .= " WHERE id = :id";

            $stmt = $conexion->prepare($sql);

            // BINDS
            $stmt->bindParam(':DNI', $DNI);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido1', $apellido1);
            $stmt->bindParam(':apellido2', $apellido2);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':id', $id);

            // SOLO si hay contraseña
            if (!empty($clave)) {
                $hashedPassword = password_hash($clave, PASSWORD_DEFAULT);
                $stmt->bindParam(':clave', $hashedPassword);
            }

            return $stmt->execute();
        } catch (Throwable $th) {
            echo "Error al actualizar el usuario: " . $th->getMessage();
            return false;
        }
    }
    
    public static function eliminar($id)
    {
        $conexion = BasedeDatos::Conectar();

        if ($conexion) {
            try {
                $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = :id");
                $stmt->bindParam(':id', $id);
                return $stmt->execute();
            } catch (Throwable $th) {
                echo "Error al eliminar el usuario: " . $th->getMessage();
                return false;
            }
        } else {
            return false;
        }
    }
}
