<?php

require_once 'core/Controller.php';
require_once 'app/modelos/fisioterapeuta.php';

class AdminFisioterapeutaControlador extends Controller
{
    private function requireAdmin(): void
    {
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Acceso restringido'));
            exit;
        }
    }

    public function index()
    {
        $this->requireAdmin();
        $this->renderAdmin('admin/fisioterapeutas/index', []);
    }

    public function formNuevo()
    {
        $this->requireAdmin();
        $this->renderAdmin('admin/fisioterapeutas/form', ['fisio' => null, 'usuario_email' => null]);
    }

    public function crear()
    {
        $this->requireAdmin();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/admin/fisioterapeutas/nuevo'));
            exit;
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $especialidad = trim((string) ($_POST['especialidad'] ?? ''));

        if ($nombre === '' || mb_strlen($nombre) > 100 || mb_strlen($especialidad) > 100) {
            header('Location: ' . url('/admin/fisioterapeutas/nuevo') . '?error=' . rawurlencode('Nombre obligatorio y máximo 100 caracteres por campo.'));
            exit;
        }

        $emailAcceso = trim((string) ($_POST['usuario_email'] ?? ''));
        $vinc = Fisioterapeuta::resolverVinculoUsuario($emailAcceso !== '' ? $emailAcceso : null, null);
        if (!$vinc['ok']) {
            header(
                'Location: ' . url('/admin/fisioterapeutas/nuevo') . '?error=' . rawurlencode((string) ($vinc['error'] ?? 'Email de acceso no válido.'))
            );
            exit;
        }

        $uid = array_key_exists('usuario_id', $vinc) ? $vinc['usuario_id'] : null;
        $newId = Fisioterapeuta::crear($nombre, $especialidad !== '' ? $especialidad : null, $uid);
        if ($newId) {
            header('Location: ' . url('/admin/fisioterapeutas') . '?success=1');
            exit;
        }

        header('Location: ' . url('/admin/fisioterapeutas/nuevo') . '?error=' . rawurlencode('No se pudo crear el registro.'));
        exit;
    }

    public function formEditar($id)
    {
        $this->requireAdmin();
        $id = (int) $id;
        $row = $id > 0 ? Fisioterapeuta::obtenerPorId($id) : null;
        if (!$row) {
            header('Location: ' . url('/admin/fisioterapeutas') . '?error=' . rawurlencode('Fisioterapeuta no encontrado.'));
            exit;
        }

        $this->renderAdmin('admin/fisioterapeutas/form', [
            'fisio' => $row,
            'usuario_email' => Fisioterapeuta::obtenerEmailAcceso((int) $row['id']),
        ]);
    }

    public function guardarEditar()
    {
        $this->requireAdmin();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/admin/fisioterapeutas'));
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $especialidad = trim((string) ($_POST['especialidad'] ?? ''));

        if ($id <= 0 || $nombre === '' || mb_strlen($nombre) > 100 || mb_strlen($especialidad) > 100) {
            header('Location: ' . url('/admin/fisioterapeutas') . '?error=' . rawurlencode('Datos no válidos.'));
            exit;
        }

        $emailAcceso = trim((string) ($_POST['usuario_email'] ?? ''));
        $vinc = Fisioterapeuta::resolverVinculoUsuario($emailAcceso !== '' ? $emailAcceso : null, $id);
        if (!$vinc['ok']) {
            header(
                'Location: ' . url('/admin/fisioterapeutas/editar/' . $id) . '?error=' . rawurlencode((string) ($vinc['error'] ?? 'Email de acceso no válido.'))
            );
            exit;
        }
        $uid = array_key_exists('usuario_id', $vinc) ? $vinc['usuario_id'] : null;

        if (Fisioterapeuta::actualizar($id, $nombre, $especialidad !== '' ? $especialidad : null, $uid)) {
            header('Location: ' . url('/admin/fisioterapeutas') . '?updated=1');
            exit;
        }

        header('Location: ' . url('/admin/fisioterapeutas/editar/' . $id) . '?error=' . rawurlencode('No se pudieron guardar los cambios.'));
        exit;
    }

    public function eliminar($id)
    {
        $this->requireAdmin();
        $id = (int) $id;
        if ($id <= 0) {
            header('Location: ' . url('/admin/fisioterapeutas') . '?error=' . rawurlencode('ID no válido.'));
            exit;
        }

        if (Fisioterapeuta::contarCitas($id) > 0) {
            header(
                'Location: ' . url('/admin/fisioterapeutas') . '?error=' .
                rawurlencode('No se puede eliminar: tiene citas asociadas. Reasigne o gestione las citas antes.')
            );
            exit;
        }

        if (Fisioterapeuta::eliminar($id)) {
            header('Location: ' . url('/admin/fisioterapeutas') . '?deleted=1');
            exit;
        }

        header('Location: ' . url('/admin/fisioterapeutas') . '?error=' . rawurlencode('No se pudo eliminar.'));
        exit;
    }
}
