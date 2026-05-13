<?php

require_once __DIR__ . '/helpers/url.php';
require_once __DIR__ . '/helpers/form_validacion.php';
require_once __DIR__ . '/helpers/grid.php';

class Controller
{
    /**
     * Exige uno de los roles indicados antes de renderizar o mutar áreas privadas.
     *
     * @param string|array<int,string> $roles
     */
    protected function requireRole($roles, string $redirect = '/login'): void
    {
        $allowed = is_array($roles) ? $roles : [$roles];
        $rol = (string) ($_SESSION['rol'] ?? '');
        if (!in_array($rol, $allowed, true)) {
            header('Location: ' . url($redirect) . '?error=' . rawurlencode('Acceso restringido'));
            exit;
        }
    }

    /** Redirección cuando un fisioterapeuta intenta usar rutas solo de socio. */
    protected function redirigirFisioFueraPortal(): void
    {
        if (($_SESSION['rol'] ?? '') === 'fisio') {
            header(
                'Location: ' . url('/fisio') . '?error=' . rawurlencode(
                    'Esta sección solo está disponible para socios.'
                )
            );
            exit;
        }
    }

    protected function renderFrontend($vista, $data = [])
    {
        if (!empty($_SESSION['password_must_change']) && $vista !== 'frontend/cambiarClaveObligatoria') {
            header('Location: ' . url('/cuenta/cambiar-clave'));
            exit;
        }

        extract($data);
        require "app/vistas/layouts/frontend/header.php";
        require "app/vistas/$vista.php";
        require "app/vistas/layouts/frontend/footer.php";
    }

    protected function renderAdmin($vista, $data = [])
    {
        $this->requireRole(['admin', 'monitor']);
        if (!empty($_SESSION['password_must_change'])) {
            header('Location: ' . url('/cuenta/cambiar-clave'));
            exit;
        }

        extract($data);
        require "app/vistas/layouts/admin/header.php";
        require "app/vistas/layouts/admin/navbar.php";
        require "app/vistas/layouts/admin/sidebar.php";
        require "app/vistas/$vista.php";
        require "app/vistas/layouts/admin/footer.php";
    }
}
