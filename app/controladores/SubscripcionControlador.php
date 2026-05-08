<?php

require_once 'core/Controller.php';
require_once 'app/modelos/susbscripcion.php';

class SubscripcionControlador extends Controller
{
    private function requireAdmin(): void
    {
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Acceso restringido'));
            exit;
        }
    }

    public function mostrarSubscripciones()
    {
        $this->requireAdmin();
        $this->renderAdmin('admin/gestionSubscripciones', []);
    }

    public function formSubscripcion()
    {
        $this->requireAdmin();
        $this->renderAdmin('admin/formSubscripcion');
    }

    public function formEditarSubscripcion()
    {
        $this->requireAdmin();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: ' . url('/admin/gestionSubscripciones') . '?error=' . rawurlencode('Suscripción no válida'));
            exit;
        }

        $subscripcion = Subscripcion::obtenerPorId($id);
        if (!$subscripcion) {
            header('Location: ' . url('/admin/gestionSubscripciones') . '?error=' . rawurlencode('Suscripción no encontrada'));
            exit;
        }

        $this->renderAdmin('admin/formEditarSubscripcion', ['subscripcion' => $subscripcion]);
    }

    public function crearSubscripcion()
    {
        $this->requireAdmin();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/admin/formSubscripcion'));
            exit;
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $precio = trim((string) ($_POST['precio'] ?? ''));
        $duracion = trim((string) ($_POST['duracion'] ?? ''));

        if ($nombre === '' || $precio === '' || $duracion === '') {
            header('Location: ' . url('/admin/formSubscripcion') . '?error=' . rawurlencode('Todos los campos son obligatorios'));
            exit;
        }

        if (!is_numeric($precio) || (float) $precio < 0) {
            header('Location: ' . url('/admin/formSubscripcion') . '?error=' . rawurlencode('Precio no válido'));
            exit;
        }

        if (!ctype_digit((string) $duracion)) {
            header('Location: ' . url('/admin/formSubscripcion') . '?error=' . rawurlencode('La duración debe ser un número entero de meses'));
            exit;
        }

        $durInt = (int) $duracion;
        if ($durInt < 1 || $durInt > 120) {
            header('Location: ' . url('/admin/formSubscripcion') . '?error=' . rawurlencode('Duración entre 1 y 120 meses'));
            exit;
        }

        Subscripcion::crear($nombre, $precio, $durInt);
        header('Location: ' . url('/admin/gestionSubscripciones'));
        exit();
    }

    public function eliminarSubscripcion()
    {
        $this->requireAdmin();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/admin/gestionSubscripciones'));
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . url('/admin/gestionSubscripciones') . '?error=' . rawurlencode('ID no válido'));
            exit;
        }

        Subscripcion::eliminar($id);
        header('Location: ' . url('/admin/gestionSubscripciones'));
        exit();
    }

    public function editarSubscripcion()
    {
        $this->requireAdmin();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/admin/gestionSubscripciones'));
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $precio = trim((string) ($_POST['precio'] ?? ''));
        $duracion = trim((string) ($_POST['duracion'] ?? ''));

        if ($id <= 0 || $nombre === '' || $precio === '' || $duracion === '') {
            header('Location: ' . url('/admin/gestionSubscripciones') . '?error=' . rawurlencode('Datos incompletos'));
            exit;
        }

        if (!is_numeric($precio) || (float) $precio < 0) {
            header('Location: ' . url('/admin/formEditarSubscripcion?id=' . $id) . '&error=' . rawurlencode('Precio no válido'));
            exit;
        }

        if (!ctype_digit((string) $duracion)) {
            header('Location: ' . url('/admin/formEditarSubscripcion?id=' . $id) . '&error=' . rawurlencode('La duración debe ser un número entero'));
            exit;
        }

        $durInt = (int) $duracion;
        if ($durInt < 1 || $durInt > 120) {
            header('Location: ' . url('/admin/formEditarSubscripcion?id=' . $id) . '&error=' . rawurlencode('Duración entre 1 y 120 meses'));
            exit;
        }

        Subscripcion::actualizar($id, $nombre, $precio, $durInt);
        header('Location: ' . url('/admin/gestionSubscripciones'));
        exit();
    }
}
