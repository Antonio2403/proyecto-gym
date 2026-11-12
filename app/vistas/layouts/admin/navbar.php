<?php
$rolPanel = $_SESSION['rol'] ?? '';
$esAdmin = ($rolPanel === 'admin');
$gpAdminInicio = $esAdmin ? url('/admin') : url('/inicioMonitor');
?>
<nav class="navbar navbar-expand-lg gp-admin-topnav gp-vt-chrome sticky top-0 z-[1020]">
    <div class="relative w-full px-4 lg:px-6">
        <button class="navbar-toggler border-0 shadow-none" type="button" data-gp-toggle="collapse"
                data-gp-target="#adminTopNav" aria-controls="adminTopNav" aria-expanded="false" aria-label="Menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a class="navbar-brand text-white" href="<?= htmlspecialchars(url('/inicio')) ?>">
            <img src="<?= htmlspecialchars(asset('img/logo-spartum.png')) ?>?v=20261112a" alt="Spartum Gym" width="220" height="80">
        </a>

        <div class="collapse navbar-collapse" id="adminTopNav">
            <ul class="navbar-nav mx-auto lg:gap-2 lg:items-center">
                <?php if ($esAdmin): ?>
                    <li class="nav-item lg:hidden">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/admin/verClientes')) ?>">Clientes</a>
                    </li>
                    <li class="nav-item lg:hidden">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/admin/fisioterapeutas')) ?>">Fisios</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item lg:hidden">
                    <a class="nav-link" href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>">Salas</a>
                </li>
                <?php if ($esAdmin): ?>
                    <li class="nav-item lg:hidden">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/admin/feedback')) ?>">Feedback</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item lg:hidden">
                    <a class="nav-link" href="<?= htmlspecialchars($gpAdminInicio) ?>">Inicio</a>
                </li>
                <li class="nav-item lg:hidden">
                    <a class="nav-link" href="<?= htmlspecialchars(url('/cuenta/perfil')) ?>">Mi perfil</a>
                </li>
                <li class="nav-item lg:hidden">
                    <a class="nav-link" href="<?= htmlspecialchars(url('/cuenta/cambiar-clave')) ?>">Cambiar contraseña</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto lg:items-center flex-wrap gap-1">
                <?php if ($esAdmin): ?>
                    <li class="nav-item hidden xl:block">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/admin/verClientes')) ?>">Clientes</a>
                    </li>
                    <li class="nav-item hidden xl:block">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/admin/fisioterapeutas')) ?>">Fisioterapeutas</a>
                    </li>
                    <li class="nav-item hidden lg:block">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/admin/gestionarActividades')) ?>">Actividades</a>
                    </li>
                    <li class="nav-item hidden lg:block">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/admin/gestionSubscripciones')) ?>">Suscripciones</a>
                    </li>
                    <li class="nav-item hidden lg:block">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/admin/feedback')) ?>">Feedback</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="btn btn-outline-light btn-sm font-semibold px-4 lg:ms-2"
                       href="<?= htmlspecialchars(url('/logout')) ?>">Salir</a>
                </li>
            </ul>
        </div>

        <button type="button" class="gp-nav-backdrop" data-gp-nav-backdrop hidden aria-label="Cerrar menú"></button>
    </div>
</nav>

<?php require __DIR__ . '/../partials/gp_flash_banner.php'; ?>
