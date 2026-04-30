<?php

require_once "core/Controller.php";
require_once "app/modelos/usuario.php";
require_once "app/modelos/cliente.php";

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

    public function formEditarCliente()
    {
        $cliente_id = Cliente::IdClientePorUsuarioId($_SESSION['usuario_id']);

        if (!$cliente_id) {
            die("Cliente no encontrado");
        }
        $cliente = Cliente::obtenerPorId($cliente_id);
        if ($cliente) {
            $this->renderFrontend("cliente/formEditarCliente", ["cliente" => $cliente]);
        } else {
            echo "Cliente no encontrado.";
        }
    }

public function editarCliente()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validar que el ID no esté vacío
        if (empty($_POST['id'])) {
            header("Location: /proyecto-gym/inicio?error=ID de usuario no válido");
            exit;
        }

        $id = $_POST['id'];
        $clave = !empty($_POST['clave']) ? $_POST['clave'] : null;

        // Intentamos actualizar la base de datos
        if (Usuario::actualizar(
            $id,
            $_POST['DNI'],
            $_POST['nombre'],
            $_POST['apellido1'],
            $_POST['apellido2'],
            $_POST['email'],
            $clave,
            $_POST['telefono']
        )) {
            
            // ¡AQUÍ ESTÁ LA MAGIA! ✨
            // Como la base de datos ya se actualizó, actualizamos la sesión
            // con los datos que acaban de llegar del formulario.
            $_SESSION['nombre'] = $_POST['nombre'];
            $_SESSION['email'] = $_POST['email'];
            // Si guardas el apellido u otros datos en la sesión en el futuro, 
            // también debes actualizarlos aquí.

            header("Location: /proyecto-gym/inicio?success=Perfil actualizado correctamente");
            exit;
        } else {
            header("Location: /proyecto-gym/inicio?error=Error al editar cliente");
            exit;
        }
    } else {
        echo "Método no permitido.";
    }
}
}
