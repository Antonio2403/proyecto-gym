<?php

class Controller
{

    protected function renderFrontend($vista)
    {
        require "app/vistas/layouts/frontend/header.php";
        require "app/vistas/$vista.php";
        require "app/vistas/layouts/frontend/footer.php";
    }

    protected function renderAdmin($vista)
{
    require "app/vistas/layouts/admin/header.php";
    require "app/vistas/layouts/admin/navbar.php";
    require "app/vistas/layouts/admin/sidebar.php";
    require "app/vistas/$vista.php";
    require "app/vistas/layouts/admin/footer.php";
}
}
