<?php
$rol = $_SESSION['rol'] ?? '';
$dashboardHref = $rol === 'admin' ? url('/admin') : url('/inicioMonitor');
$currentPath = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
/** @psalm-suppress PossiblyFalseArgument */
$baseRouter = rtrim(router_server_base_path(), '/');
if ($baseRouter !== '/' && $baseRouter !== '' && str_starts_with($currentPath, $baseRouter)) {
    $currentPath = substr($currentPath, strlen($baseRouter)) ?: '/';
}

function gp_admin_nav_active(string $prefix, string $currentPath): string
{
    $p = '/' . ltrim($prefix, '/');
    if ($p !== '/' && str_starts_with($currentPath, $p)) {
        return ' active';
    }

    return '';
}
?>
<div class="gp-admin-page flex-grow-1">
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="row g-4 align-items-start">
            <aside class="col-lg-3 col-xl-2">
                <div class="gp-admin-sidecard p-3 sticky-lg-top gp-vt-chrome" style="top: 5rem;">
                    <div class="gp-admin-user-line">
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?></div>
                        <span class="badge rounded-pill mt-1 bg-secondary bg-opacity-25 text-secondary-emphasis">
                            <?= htmlspecialchars($rol) ?>
                        </span>
                    </div>

                    <nav class="nav flex-column gp-admin-nav gap-1 mt-2">
                        <a href="<?= htmlspecialchars($dashboardHref) ?>"
                           class="nav-link gp-admin-nav-link<?= $currentPath === '/admin' || $currentPath === '/inicioAdmin' || $currentPath === '/inicioMonitor' ? ' active' : '' ?>">
                            <i class="fas fa-home me-2 text-opacity-75"></i> Inicio
                        </a>

                        <div class="gp-admin-nav-heading">Tu cuenta</div>
                        <a href="<?= htmlspecialchars(url('/cuenta/perfil')) ?>"
                           class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/cuenta/perfil', $currentPath) ?>">
                            <i class="fas fa-user-pen me-2 text-opacity-75"></i> Mi perfil
                        </a>

                        <div class="gp-admin-nav-heading">Operaciones</div>
                        <a href="<?= htmlspecialchars(url('/cuenta/cambiar-clave')) ?>"
                           class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/cuenta/cambiar-clave', $currentPath) ?>">
                            <i class="fas fa-key me-2 text-opacity-75"></i> Cambiar contraseña
                        </a>

                        <a href="<?= htmlspecialchars(url('/logout')) ?>"
                           class="nav-link gp-admin-nav-link text-danger fw-semibold mt-3 border border-danger border-opacity-25 rounded">
                            <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
                        </a>
                    </nav>
                </div>
            </aside>
            <div class="col-lg-9 col-xl-10 gp-admin-content gp-vt-page">
