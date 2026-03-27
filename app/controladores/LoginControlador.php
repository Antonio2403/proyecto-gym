<?php

require_once "core/Controller.php";
require_once "app/modelos/usuario.php";


class LoginControlador extends Controller
{
    public function mostrarLogin()
    {
        $this->view("login");
    }
public function login()
{

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $email = $_POST['email'];
        $clave = $_POST['clave'];

        $usuario = Usuario::obtenerPorEmail($email);

        if ($usuario && password_verify($clave, $usuario->getClave())
        ) {

            $_SESSION['usuario_id'] = $usuario->getId();
            $_SESSION['email'] = $usuario->getEmail();
            $_SESSION['nombre'] = $usuario->getNombre();

            $conexion = BasedeDatos::Conectar();
            $_SESSION['rol'] = 'cliente';

            $stmt = $conexion->prepare("SELECT * FROM administradores WHERE usuario_id = :id");
            $stmt->bindValue(':id', $usuario->getId());
            $stmt->execute();

            if ($stmt->fetch()) {
                $_SESSION['rol'] = 'admin';
                header("Location: /proyecto-gym/inicioAdmin");
                exit;
            } else {
                $stmt = $conexion->prepare("SELECT * FROM monitores WHERE usuario_id = :id");
                $stmt->bindValue(':id', $usuario->getId());
                $stmt->execute();

                if ($stmt->fetch()) {
                    $_SESSION['rol'] = 'monitor';
                    header("Location: /proyecto-gym/inicioMonitor");
                    exit;
                }
            }

            header("Location: /proyecto-gym/inicioUsuario");
            exit;

        } else {

            echo "Email o contraseña incorrectos";

        }

    }

}
    public function logout()
    {
        session_destroy();
        header("Location: /proyecto-gym/login");
        exit;
    }
}
