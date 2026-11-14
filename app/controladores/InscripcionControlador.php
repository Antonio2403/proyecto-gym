<?php

require_once 'app/modelos/inscripcion.php';
require_once 'app/modelos/actividades.php';
require_once 'app/modelos/cliente_subscripcion.php';
require_once 'core/Controller.php';

use Spartum\Servicios\ReservaActividadServicio;

class InscripcionControlador extends Controller
{
    public function misIncripciones()
    {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Tienes que iniciar sesión para ver tus actividades.'));
            exit;
        }

        $this->redirigirFisioFueraPortal();
        $uid = (int) ($_SESSION['usuario_id'] ?? 0);
        $tienePlan = $uid > 0 && ClienteSubscripcion::tieneSuscripcionActivaPorUsuarioId($uid);
        $inscripciones = Inscripcion::obtenerInscripciones();
        $this->renderFrontend('frontend/verMisActividades', [
            'inscripciones' => $inscripciones,
            'tiene_plan_activo' => $tienePlan,
        ]);
    }

    public function cancelar()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/usuario/inscripciones/mis-inscripciones'));
            exit;
        }

        $this->redirigirFisioFueraPortal();

        $resultado = (new ReservaActividadServicio())->cancelar(
            (int) ($_SESSION['usuario_id'] ?? 0),
            (int) ($_POST['inscripcion_id'] ?? 0)
        );
        $this->redirigirResultadoReserva($resultado);
    }

    public function inscribirse()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . url('/usuario/actividades'));
            exit;
        }

        $this->redirigirFisioFueraPortal();

        $actividad_id = (int) ($_POST['actividad_id'] ?? 0);
        $fecha_sesion = trim((string) ($_POST['fecha_sesion'] ?? ''));
        $resultado = (new ReservaActividadServicio())->reservar(
            (int) ($_SESSION['usuario_id'] ?? 0),
            $actividad_id,
            $fecha_sesion
        );
        $this->redirigirResultadoReserva($resultado);
    }

    /**
     * @param array{estado: string, mensaje: string, ruta: string, parametros?: array<string, scalar>} $resultado
     */
    private function redirigirResultadoReserva(array $resultado): void
    {
        $params = $resultado['parametros'] ?? [];
        $params[$resultado['estado'] === 'success' ? 'success' : $resultado['estado']] = $resultado['mensaje'];
        header('Location: ' . url($resultado['ruta']) . '?' . http_build_query($params));
        exit;
    }
}
