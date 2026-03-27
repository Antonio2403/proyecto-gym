<?php

class LoginControlador
{
    public function mostrarLogin()
    {
        require_once "vistas/login.php";
    }
    public function iniciarSesion($email, $clave)
    {
        try {
            $conexion = BasedeDatos::Conectar();
            if (!$conexion) {
                return false;
            }

            $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($clave, $usuario['clave'])) {
                    session_start();
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre'];
                    return true;
                }
            }
            return false;
        } catch (\Throwable $th) {
            echo "Error al iniciar sesión: " . $th->getMessage();
            return false;
        }
    }
}

?>