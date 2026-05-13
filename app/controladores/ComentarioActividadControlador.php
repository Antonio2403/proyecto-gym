<?php

require_once 'core/Controller.php';
require_once 'app/modelos/actividades.php';
require_once 'app/modelos/comentario.php';
require_once 'app/modelos/cliente.php';

class ComentarioActividadControlador extends Controller
{
    private function codigoDiaDesdeFecha(string $ymd): string
    {
        $n = (int) (new DateTime($ymd))->format('N');
        $map = [1 => 'L', 2 => 'M', 3 => 'X', 4 => 'J', 5 => 'V', 6 => 'S', 7 => 'D'];

        return $map[$n];
    }

    public function ver()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login') . '?error=' . urlencode('Debes iniciar sesión'));
            exit;
        }

        $this->redirigirFisioFueraPortal();

        $actividadId = isset($_GET['actividad_id']) ? (int) $_GET['actividad_id'] : 0;
        $fecha = trim($_GET['fecha'] ?? '');

        if ($actividadId <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            header('Location: ' . url('/usuario/actividades') . '?error=' . urlencode('Sesión no válida'));
            exit;
        }

        $actividad = Actividad::obtenerPorId($actividadId);
        if (!$actividad) {
            header('Location: ' . url('/usuario/actividades') . '?error=' . urlencode('Actividad no encontrada'));
            exit;
        }

        $esRecurrente = (int) ($actividad['recurrente'] ?? 1) === 1;
        $diasAct = Actividad::diasParaActividadId($actividadId);
        $codFecha = $this->codigoDiaDesdeFecha($fecha);
        if ($esRecurrente && !in_array($codFecha, $diasAct, true)) {
            header('Location: ' . url('/usuario/actividades') . '?error=' . urlencode('La fecha no coincide con esta actividad'));
            exit;
        }
        if (!$esRecurrente && date('Y-m-d', strtotime($actividad['fecha_inicio'])) !== $fecha) {
            header('Location: ' . url('/usuario/actividades') . '?error=' . urlencode('Fecha no válida para esta actividad'));
            exit;
        }

        $usuarioId = (int) $_SESSION['usuario_id'];
        $tieneReservaSesion = Comentario::usuarioTieneReservaEnSesion($usuarioId, $actividadId, $fecha);
        $asistioRegistrado = Comentario::usuarioAsistioSesionMarcada($usuarioId, $actividadId, $fecha);
        $sesionPasada = Comentario::sesionHaPasado($actividad, $fecha);
        $puedeComentar = $asistioRegistrado && $sesionPasada;

        $perPage = 10;
        if (isset($_GET['por_pagina'])) {
            $perPage = (int) $_GET['por_pagina'];
        }
        $page = isset($_GET['p']) ? (int) $_GET['p'] : 1;
        if ($page < 1) {
            $page = 1;
        }

        $ordenComentarios = strtolower(trim($_GET['orden'] ?? 'asc'));
        if ($ordenComentarios !== 'desc') {
            $ordenComentarios = 'asc';
        }

        $paginaComentarios = Comentario::listarPorSesionPaginado(
            $actividadId,
            $fecha,
            $page,
            $perPage,
            $ordenComentarios
        );

        $this->renderFrontend('frontend/comentariosActividad', [
            'actividad' => $actividad,
            'fecha_ocurrencia' => $fecha,
            'comentarios' => $paginaComentarios['rows'],
            'comentarios_total' => $paginaComentarios['total'],
            'comentarios_page' => $paginaComentarios['page'],
            'comentarios_per_page' => $paginaComentarios['per_page'],
            'comentarios_total_pages' => $paginaComentarios['total_pages'],
            'comentarios_orden' => $paginaComentarios['orden'],
            'puedeComentar' => $puedeComentar,
            'tiene_reserva_sesion' => $tieneReservaSesion,
            'asistio_registrado' => $asistioRegistrado,
            'sesionPasada' => $sesionPasada,
        ]);
    }

    public function guardar()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login') . '?error=' . urlencode('Debes iniciar sesión'));
            exit;
        }

        $this->redirigirFisioFueraPortal();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/usuario/actividades'));
            exit;
        }

        $actividadId = (int) ($_POST['actividad_id'] ?? 0);
        $fecha = trim($_POST['fecha_ocurrencia'] ?? '');
        $texto = trim($_POST['texto'] ?? '');
        $ordenPost = strtolower(trim($_POST['orden'] ?? 'asc'));
        if ($ordenPost !== 'desc') {
            $ordenPost = 'asc';
        }
        $perPagePost = isset($_POST['por_pagina']) ? (int) $_POST['por_pagina'] : 10;
        $perPagePost = min(50, max(5, $perPagePost));

        $qsParams = [
            'actividad_id' => $actividadId,
            'fecha' => $fecha,
            'orden' => $ordenPost,
            'por_pagina' => $perPagePost,
        ];
        $qs = http_build_query($qsParams);

        if ($actividadId <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            header('Location: ' . url('/usuario/actividades/sesion/comentarios') . '?' . $qs . '&error=' . urlencode('Datos inválidos'));
            exit;
        }

        if (mb_strlen($texto) < 3) {
            header('Location: ' . url('/usuario/actividades/sesion/comentarios') . '?' . $qs . '&error=' . urlencode('El comentario debe tener al menos 3 caracteres'));
            exit;
        }
        if (mb_strlen($texto) > 2000) {
            header('Location: ' . url('/usuario/actividades/sesion/comentarios') . '?' . $qs . '&error=' . urlencode('El comentario es demasiado largo (máx. 2000 caracteres)'));
            exit;
        }

        $actividad = Actividad::obtenerPorId($actividadId);
        if (!$actividad) {
            header('Location: ' . url('/usuario/actividades') . '?error=' . urlencode('Actividad no encontrada'));
            exit;
        }

        $usuarioId = (int) $_SESSION['usuario_id'];
        if (!Comentario::usuarioAsistioSesionMarcada($usuarioId, $actividadId, $fecha)) {
            header('Location: ' . url('/usuario/actividades/sesion/comentarios') . '?' . $qs . '&error=' . urlencode('Solo pueden comentar quienes tienen la asistencia registrada en esta sesión'));
            exit;
        }

        if (!Comentario::sesionHaPasado($actividad, $fecha)) {
            header('Location: ' . url('/usuario/actividades/sesion/comentarios') . '?' . $qs . '&error=' . urlencode('Aún no puedes comentar: la sesión no ha terminado'));
            exit;
        }

        $clienteId = Cliente::IdClientePorUsuarioId($usuarioId);
        if (!$clienteId) {
            header('Location: ' . url('/usuario/actividades/sesion/comentarios') . '?' . $qs . '&error=' . urlencode('Cliente no encontrado'));
            exit;
        }

        if (Comentario::crear((int) $clienteId, $actividadId, $fecha, $texto)) {
            $perPage = $perPagePost;
            $nuevoTotal = Comentario::contarPorSesion($actividadId, $fecha);
            $ultimaPagina = max(1, (int) ceil($nuevoTotal / $perPage));
            $paginaVer = $ordenPost === 'desc' ? 1 : $ultimaPagina;
            $redir = array_merge($qsParams, ['p' => $paginaVer, 'success' => '1']);
            header('Location: ' . url('/usuario/actividades/sesion/comentarios') . '?' . http_build_query($redir));
        } else {
            header('Location: ' . url('/usuario/actividades/sesion/comentarios') . '?' . $qs . '&error=' . urlencode('No se pudo guardar el comentario'));
        }
        exit;
    }
}
