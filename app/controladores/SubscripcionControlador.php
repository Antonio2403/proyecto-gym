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
        $numeroClasesRaw = trim((string) ($_POST['numero_clases'] ?? '0'));
        $fisio = (string) ($_POST['fisio'] ?? 'N');
        $enOferta = !empty($_POST['en_oferta']) ? 1 : 0;
        $ofertaMotivo = trim((string) ($_POST['oferta_motivo'] ?? ''));
        $ofertaFin = trim((string) ($_POST['oferta_fin'] ?? ''));

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

        if (!ctype_digit($numeroClasesRaw)) {
            header('Location: ' . url('/admin/formSubscripcion') . '?error=' . rawurlencode('Número de clases no válido'));
            exit;
        }
        $numeroClases = (int) $numeroClasesRaw;
        if ($numeroClases < 0 || $numeroClases > 99) {
            header('Location: ' . url('/admin/formSubscripcion') . '?error=' . rawurlencode('Número de clases entre 0 y 99'));
            exit;
        }
        $fisio = $fisio === 'S' ? 'S' : 'N';
        [$promoOk, $promoMsg, $ofertaFinSql] = $this->normalizarPromocion($enOferta, $ofertaMotivo, $ofertaFin);
        if (!$promoOk) {
            header('Location: ' . url('/admin/formSubscripcion') . '?error=' . rawurlencode($promoMsg));
            exit;
        }

        Subscripcion::crear($nombre, $precio, $durInt, $numeroClases, $fisio, $enOferta, $ofertaMotivo !== '' ? $ofertaMotivo : null, $ofertaFinSql);
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

        if (!Subscripcion::eliminar($id)) {
            header('Location: ' . url('/admin/gestionSubscripciones') . '?error=' . rawurlencode('No se pudo retirar la suscripción'));
            exit;
        }
        header('Location: ' . url('/admin/gestionSubscripciones') . '?success=' . rawurlencode('Suscripción retirada del catálogo'));
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
        $numeroClasesRaw = trim((string) ($_POST['numero_clases'] ?? '0'));
        $fisio = (string) ($_POST['fisio'] ?? 'N');
        $enOferta = !empty($_POST['en_oferta']) ? 1 : 0;
        $ofertaMotivo = trim((string) ($_POST['oferta_motivo'] ?? ''));
        $ofertaFin = trim((string) ($_POST['oferta_fin'] ?? ''));

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

        if (!ctype_digit($numeroClasesRaw)) {
            header('Location: ' . url('/admin/formEditarSubscripcion?id=' . $id) . '&error=' . rawurlencode('Número de clases no válido'));
            exit;
        }
        $numeroClases = (int) $numeroClasesRaw;
        if ($numeroClases < 0 || $numeroClases > 99) {
            header('Location: ' . url('/admin/formEditarSubscripcion?id=' . $id) . '&error=' . rawurlencode('Número de clases entre 0 y 99'));
            exit;
        }
        $fisio = $fisio === 'S' ? 'S' : 'N';
        [$promoOk, $promoMsg, $ofertaFinSql] = $this->normalizarPromocion($enOferta, $ofertaMotivo, $ofertaFin);
        if (!$promoOk) {
            header('Location: ' . url('/admin/formEditarSubscripcion?id=' . $id) . '&error=' . rawurlencode($promoMsg));
            exit;
        }

        Subscripcion::actualizar($id, $nombre, $precio, $durInt, $numeroClases, $fisio, $enOferta, $ofertaMotivo !== '' ? $ofertaMotivo : null, $ofertaFinSql);
        header('Location: ' . url('/admin/gestionSubscripciones'));
        exit();
    }

    /**
     * @return array{0: bool, 1: string, 2: string|null}
     */
    private function normalizarPromocion(int $enOferta, string $motivo, string $fechaInput): array
    {
        if ($enOferta !== 1) {
            return [true, '', null];
        }

        if ($motivo === '') {
            return [false, 'Indica el motivo de la promoción', null];
        }

        if ($fechaInput === '') {
            return [false, 'Indica la fecha y hora de fin de la promoción', null];
        }

        $dt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $fechaInput);
        if (!$dt) {
            return [false, 'Fecha de fin de promoción no válida', null];
        }

        if ($dt <= new DateTimeImmutable('now')) {
            return [false, 'La promoción debe finalizar en una fecha futura', null];
        }

        return [true, '', $dt->format('Y-m-d H:i:s')];
    }
}
