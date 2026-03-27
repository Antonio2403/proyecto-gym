<?php

require_once "core/Controller.php";
require_once "app/modelos/usuario.php";

class UsuarioControlador extends Controller
{

    public function registrar()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $usuario = new Usuario(
                $_POST['DNI'],
                $_POST['nombre'],
                $_POST['apellido1'],
                $_POST['apellido2'],
                $_POST['email'],
                $_POST['clave'],
                $_POST['telefono']
            );

            $resultado = $usuario->registrar();

            if ($resultado) {

                header("Location: /proyecto-gym/login");
                exit;

            } else {

                echo "Error al registrar usuario";

            }

        }

    }

}