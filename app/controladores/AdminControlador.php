<?php

require_once "core/Controller.php";
require_once "app/modelos/admin.php";
require_once "app/modelos/solicitud.php";
require_once "app/modelos/monitor.php";

class AdminControlador extends Controller
{
    private function requireAdmin(): void
    {
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Acceso restringido'));
            exit;
        }
    }

    public function verClientes()
    {
        $this->requireAdmin();
        $this->renderAdmin('admin/verClientes', []);
    }

    public function verMonitores()
    {
        $this->requireAdmin();
        $this->renderAdmin("admin/verMonitores", []);
    }

    public function formEditarMonitor($id)
    {
        $this->requireAdmin();
        $monitor = Monitor::obtenerPorId($id);
        if ($monitor) {
            $this->renderAdmin("admin/formEditarMonitor", ["monitor" => $monitor]);
        } else {
            echo "Monitor no encontrado.";
        }
    }

    public function editarMonitor()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/admin/verMonitores'));
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $DNI = trim((string) ($_POST['DNI'] ?? ''));
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $apellido1 = trim((string) ($_POST['apellido1'] ?? ''));
        $apellido2 = trim((string) ($_POST['apellido2'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $claveRaw = trim((string) ($_POST['clave'] ?? ''));
        $clave = $claveRaw !== '' ? $claveRaw : null;
        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        $especialidad = trim((string) ($_POST['especialidad'] ?? ''));
        $disponibilidad = trim((string) ($_POST['disponibilidad'] ?? ''));

        if ($id <= 0 || $DNI === '' || $nombre === '' || $apellido1 === '' || $email === '' || $telefono === '' || $especialidad === '' || $disponibilidad === '') {
            header('Location: ' . url('/admin/verMonitores') . '?error=' . rawurlencode('Datos incompletos'));
            exit;
        }

        if (!fv_documento_identidad_es($DNI)) {
            header('Location: ' . url('/admin/verMonitores') . '?error=' . rawurlencode('DNI o NIE no válido'));
            exit;
        }

        if (!fv_email_valido($email)) {
            header('Location: ' . url('/admin/verMonitores') . '?error=' . rawurlencode('Email no válido'));
            exit;
        }

        if ($clave !== null && !fv_clave_registro_valida($clave)) {
            header('Location: ' . url('/admin/verMonitores') . '?error=' . rawurlencode('La contraseña debe tener al menos 8 caracteres'));
            exit;
        }

        if (Monitor::actualizar(
            $id,
            $DNI,
            $nombre,
            $apellido1,
            $apellido2,
            $email,
            $clave,
            $telefono,
            $especialidad,
            $disponibilidad
        )) {
            header('Location: ' . url('/admin/verMonitores'));
            exit;
        }

        header('Location: ' . url('/admin/verMonitores') . '?error=' . rawurlencode('Error al editar monitor'));
        exit;
    }

    public function registrarMonitor()
    {
        $this->requireAdmin();
        $this->renderAdmin("admin/registrarMonitor");
    }
    public function crearMonitor()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/admin/registrarMonitor'));
            exit;
        }

        $DNI = trim((string) ($_POST['DNI'] ?? ''));
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $apellido1 = trim((string) ($_POST['apellido1'] ?? ''));
        $apellido2 = trim((string) ($_POST['apellido2'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $clave = (string) ($_POST['clave'] ?? '');
        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        $especialidad = trim((string) ($_POST['especialidad'] ?? ''));
        $disponibilidad = trim((string) ($_POST['disponibilidad'] ?? ''));

        if ($DNI === '' || $nombre === '' || $apellido1 === '' || $email === '' || $clave === '' || $telefono === '' || $especialidad === '' || $disponibilidad === '') {
            header('Location: ' . url('/admin/registrarMonitor') . '?error=' . rawurlencode('Todos los campos obligatorios deben estar rellenos'));
            exit;
        }

        if (!fv_documento_identidad_es($DNI)) {
            header('Location: ' . url('/admin/registrarMonitor') . '?error=' . rawurlencode('DNI o NIE no válido'));
            exit;
        }

        if (!fv_email_valido($email)) {
            header('Location: ' . url('/admin/registrarMonitor') . '?error=' . rawurlencode('Email no válido'));
            exit;
        }

        if (!fv_clave_registro_valida($clave)) {
            header('Location: ' . url('/admin/registrarMonitor') . '?error=' . rawurlencode('La contraseña debe tener al menos 8 caracteres'));
            exit;
        }

        $datos = [
            'DNI' => $DNI,
            'nombre' => $nombre,
            'apellido1' => $apellido1,
            'apellido2' => $apellido2,
            'email' => $email,
            'clave' => $clave,
            'telefono' => $telefono,
            'especialidad' => $especialidad,
            'disponibilidad' => $disponibilidad,
        ];

        if (Admin::crearMonitor($datos)) {
            header('Location: ' . url('/admin/verMonitores'));
            exit;
        }

        header('Location: ' . url('/admin/registrarMonitor') . '?error=' . rawurlencode('No se pudo crear el monitor'));
        exit;
    }

    public function eliminarMonitor($id)
    {
        $this->requireAdmin();
        if (Monitor::eliminar((int) $id)) {
            header('Location: ' . url('/admin/verMonitores'));
        } else {
            header('Location: ' . url('/admin/verMonitores') . '?error=' . rawurlencode('No se pudo eliminar el monitor'));
        }
        exit;
    }

    public function verSolicitudes()
    {
        $this->requireAdmin();
        $this->renderAdmin("admin/verSolicitudes", []);
    }

    public function verSolicitudesAprobadas()
    {
        $this->requireAdmin();
        $this->renderAdmin("admin/verSolicitudesAprobadas", []);
    }

    public function verSolicitudesRechazadas()
    {
        $this->requireAdmin();
        $this->renderAdmin("admin/verSolicitudesRechazadas", []);
    }

    public function aprobarSolicitud()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/admin/verSolicitudes'));
            exit;
        }

        $id_admin = (int) ($_SESSION['usuario_id'] ?? 0);
        $id = (int) ($_POST['id'] ?? 0);
        $estado = (string) ($_POST['estado'] ?? '');

        if ($id <= 0 || !in_array($estado, ['A', 'R'], true)) {
            header('Location: ' . url('/admin/verSolicitudes') . '?error=' . rawurlencode('Solicitud no válida'));
            exit;
        }

        if (Solicitud::cambiarEstado($id, $estado, $id_admin)) {
            $msg = $estado === 'A' ? 'Solicitud aprobada' : 'Solicitud rechazada';
            header('Location: ' . url('/admin/verSolicitudes') . '?success=' . rawurlencode($msg));
            exit;
        }

        header('Location: ' . url('/admin/verSolicitudes') . '?error=' . rawurlencode('No se pudo actualizar la solicitud'));
        exit;
    }
}
