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
        $json = (string) ($_POST['formato'] ?? '') === 'json';
        if (Cliente::IdClientePorUsuarioId($usuarioId) === null) {
            $this->respuestaGenerar($json, false, 'Tu cuenta no tiene ficha de socio.');
        }
        try {
            $pase = AccesoCentro::generar($usuarioId);
        } catch (Throwable $e) {
            error_log('[AccesoCentro] ' . $e->getMessage());
            $this->respuestaGenerar($json, false, 'No se pudo generar el código.');
        }
        if ($json) {
            $this->respuestaGenerar($json, true, '', $pase);
        }
        $texto = $pase['tipo'] === 'entrada'
            ? 'Código de entrada generado.'
            : 'Código de salida generado.';
        header('Location: ' . url('/usuario/acceso') . '?success=' . rawurlencode($texto));
        exit;
    }

    /**
     * @param array{tipo?: string, token?: string, creado_en?: string} $pase
     */
    private function respuestaGenerar(bool $json, bool $ok, string $error = '', array $pase = []): void
    {
        if (!$json) {
            $destino = $ok
                ? url('/usuario/acceso') . '?success=' . rawurlencode($pase['tipo'] === 'entrada' ? 'Código de entrada generado.' : 'Código de salida generado.')
                : url('/usuario/acceso') . '?error=' . rawurlencode($error);
            header('Location: ' . $destino);
            exit;
        }
        header('Content-Type: application/json; charset=utf-8');
        if (!$ok) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => $error], JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo json_encode([
            'ok' => true,
            'tipo' => (string) ($pase['tipo'] ?? ''),
            'url' => app_url_absolute('/acceso/' . (string) ($pase['token'] ?? '')),
            'creado_en' => (string) ($pase['creado_en'] ?? ''),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
