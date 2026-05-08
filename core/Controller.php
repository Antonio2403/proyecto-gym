<?php

require_once __DIR__ . '/helpers/url.php';
require_once __DIR__ . '/helpers/form_validacion.php';
require_once __DIR__ . '/helpers/grid.php';

class Controller
{
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
        extract($data);
        require "app/vistas/layouts/frontend/header.php";
        require "app/vistas/$vista.php";
        require "app/vistas/layouts/frontend/footer.php";
    }

    protected function renderAdmin($vista, $data = [])
    {
        extract($data);
        require "app/vistas/layouts/admin/header.php";
        require "app/vistas/layouts/admin/navbar.php";
        require "app/vistas/layouts/admin/sidebar.php";
        require "app/vistas/$vista.php";
        require "app/vistas/layouts/admin/footer.php";
    }
}
