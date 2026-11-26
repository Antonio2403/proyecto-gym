<?php
$rol = $_SESSION['rol'] ?? '';
$esAdmin = ($rol === 'admin');
$dashboardHref = $esAdmin ? url('/admin') : url('/inicioMonitor');
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
<div class="gp-admin-page grow">
    <div class="relative w-full px-4 lg:px-6 py-6">
        <div class="items-start flex flex-wrap -mx-3 -mt-6">
            <aside class="w-full lg:w-1/4 xl:w-1/6 max-w-full shrink-0 px-3 mt-6">
                <div class="gp-admin-sidecard p-4 lg:sticky lg:top-0 lg:z-20 gp-vt-chrome" style="top: 5rem;">
                    <div class="gp-admin-user-line">
                        <div class="font-semibold text-slate-800"><?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?></div>
                        <span class="badge rounded-full mt-1 bg-slate-500/25 text-slate-500">
                            <?= htmlspecialchars($rol) ?>
                        </span>
                    </div>

                    <nav class="nav flex-col gp-admin-nav gap-1 mt-2">
                        <a href="<?= htmlspecialchars($dashboardHref) ?>"
                           class="nav-link gp-admin-nav-link<?= $currentPath === '/admin' || $currentPath === '/inicioAdmin' || $currentPath === '/inicioMonitor' ? ' active' : '' ?>">
                            <i class="fas fa-home me-2 opacity-75"></i> Inicio
                        </a>

                        <div class="gp-admin-nav-heading">Tu cuenta</div>
                        <a href="<?= htmlspecialchars(url('/cuenta/perfil')) ?>"
                           class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/cuenta/perfil', $currentPath) ?>">
                            <i class="fas fa-user-pen me-2 opacity-75"></i> Mi perfil
                        </a>

                        <div class="gp-admin-nav-heading">Operaciones</div>
                        <?php if ($esAdmin): ?>
                            <a href="<?= htmlspecialchars(url('/admin/verClientes')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/admin/verClientes', $currentPath) ?>">
                                <i class="fas fa-users me-2 opacity-75"></i> Clientes
                            </a>
                            <a href="<?= htmlspecialchars(url('/admin/gestionarActividades')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/admin/gestionarActividades', $currentPath) ?>">
                                <i class="fas fa-dumbbell me-2 opacity-75"></i> Actividades
                            </a>
                            <a href="<?= htmlspecialchars(url('/admin/gestionSubscripciones')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/admin/gestionSubscripciones', $currentPath) ?>">
                                <i class="fas fa-id-card me-2 opacity-75"></i> Suscripciones
                            </a>
                            <a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/monitor/verSalas', $currentPath) ?>">
                                <i class="fas fa-door-open me-2 opacity-75"></i> Salas
                            </a>
                            <a href="<?= htmlspecialchars(url('/admin/fisioterapeutas')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/admin/fisioterapeutas', $currentPath) ?>">
                                <i class="fas fa-user-md me-2 opacity-75"></i> Fisioterapeutas
                            </a>
                            <a href="<?= htmlspecialchars(url('/admin/verSolicitudes')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/admin/verSolicitudes', $currentPath) ?>">
                                <i class="fas fa-clipboard-list me-2 opacity-75"></i> Reportes
                            </a>
                            <a href="<?= htmlspecialchars(url('/admin/feedback')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/admin/feedback', $currentPath) ?>">
                                <i class="fas fa-comments me-2 opacity-75"></i> Feedback
                            </a>
                        <?php else: ?>
                            <a href="<?= htmlspecialchars(url('/monitor/mis-clases')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/monitor/mis-clases', $currentPath) ?>">
                                <i class="fas fa-calendar-check me-2 opacity-75"></i> Mis clases
                            </a>
                            <a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/monitor/verSalas', $currentPath) ?>">
                                <i class="fas fa-door-open me-2 opacity-75"></i> Salas
                            </a>
                            <a href="<?= htmlspecialchars(url('/monitor/verMisSolicitudes')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/monitor/verMisSolicitudes', $currentPath) ?>">
                                <i class="fas fa-clipboard-list me-2 opacity-75"></i> Reportes
                            </a>
                        <?php endif; ?>

                        <a href="<?= htmlspecialchars(url('/logout')) ?>"
                           class="nav-link gp-admin-nav-link text-red-600 font-semibold mt-4 border border-red-600/25 rounded-lg">
                            <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
                        </a>
                    </nav>
                </div>
            </aside>
            <div class="gp-admin-content gp-vt-page w-full lg:w-3/4 xl:w-5/6 max-w-full shrink-0 px-3 mt-6">
