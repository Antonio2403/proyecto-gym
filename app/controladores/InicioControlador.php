<?php

require_once "core/Controller.php";

class InicioControlador extends Controller {

    public function index()
    {
        $this->renderFrontend("frontend/inicio");
    }

    public function robots(): void
    {
        require_once __DIR__ . '/../../core/helpers/seo.php';
        header('Content-Type: text/plain; charset=utf-8');
        echo gp_seo_robots_txt();
    }

    public function sitemap(): void
    {
        require_once __DIR__ . '/../../core/helpers/seo.php';
        header('Content-Type: application/xml; charset=utf-8');
        echo gp_seo_sitemap_xml();
    }

    public function quienesSomos()
    {
        $this->renderFrontend('frontend/quienesSomos');
    }

    public function aceptarCookies(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(404);
            return;
        }

        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        setcookie('gp_cookie_consent', 'accepted', [
            'expires' => time() + 365 * 86400,
            'path' => '/',
            'secure' => $https,
            'httponly' => false,
            'samesite' => 'Lax',
        ]);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
    }

    public function inicioAdmin()
    {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Debes iniciar sesión'));
            exit;
        }
        $rol = (string) ($_SESSION['rol'] ?? '');
        if ($rol === 'monitor') {
            header('Location: ' . url('/inicioMonitor'));
            exit;
        }
        if ($rol === 'fisio') {
            header('Location: ' . url('/fisio'));
            exit;
        }
        if ($rol !== 'admin') {
            header('Location: ' . url('/inicioUsuario'));
            exit;
        }

        require_once __DIR__ . '/../modelos/panel_estadisticas.php';
        $this->renderAdmin('admin/inicioAdmin', [
            'estadisticas' => PanelEstadisticas::resumenAdmin(),
        ]);
    }

    public function inicioUsuario(){
        if (($_SESSION['rol'] ?? '') === 'fisio') {
            header('Location: ' . url('/fisio'));
            exit;
        }
        $this->renderFrontend("frontend/inicioUsuario");
    }

    public function inicioMonitor()
    {
        $rol = $_SESSION['rol'] ?? '';
        if ($rol === 'admin') {
            header('Location: ' . url('/admin'));
            exit;
        }
        if ($rol !== 'monitor') {
            header('Location: ' . url('/login') . '?error=' . rawurlencode('Acceso restringido'));
            exit;
        }

        $this->renderAdmin('monitor/inicioMonitor');
    }
}
