<?php

declare(strict_types=1);

require_once 'core/Controller.php';
require_once 'app/modelos/acceso_centro.php';
require_once 'app/modelos/cliente.php';

class AccesoCentroControlador extends Controller
{
    public function panel(): void
    {
        $this->exigirCliente();
        $usuarioId = (int) $_SESSION['usuario_id'];
        $ultimo = AccesoCentro::ultimoDeUsuario($usuarioId);
        $this->renderFrontend('frontend/acceso/generar', [
            'ultimo' => $ultimo,
            'siguiente' => AccesoCentro::siguienteTipo($usuarioId),
            'cliente_id' => Cliente::IdClientePorUsuarioId($usuarioId),
        ]);
    }

    public function generar(): void
    {
        $this->exigirCliente();
        $usuarioId = (int) $_SESSION['usuario_id'];
        if (Cliente::IdClientePorUsuarioId($usuarioId) === null) {
            header('Location: ' . url('/usuario/acceso') . '?error=' . rawurlencode('Tu cuenta no tiene ficha de socio.'));
            exit;
        }
        try {
            $pase = AccesoCentro::generar($usuarioId);
        } catch (Throwable $e) {
            error_log('[AccesoCentro] ' . $e->getMessage());
            header('Location: ' . url('/usuario/acceso') . '?error=' . rawurlencode('No se pudo generar el código.'));
            exit;
        }
        $texto = $pase['tipo'] === 'entrada'
            ? 'Código de entrada generado.'
            : 'Código de salida generado.';
        header('Location: ' . url('/usuario/acceso') . '?success=' . rawurlencode($texto));
        exit;
    }

    public function ver(string $token): void
    {
        $pase = AccesoCentro::porToken($token);
        $this->renderFrontend('frontend/acceso/ver', [
            'pase' => $pase,
        ]);
    }

    private function exigirCliente(): void
    {
        if (!isset($_SESSION['usuario_id']) || (string) ($_SESSION['rol'] ?? '') !== 'cliente') {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Acceso restringido'));
            exit;
        }
    }
}
