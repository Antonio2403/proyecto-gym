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
                        <a href="<?= htmlspecialchars(url('/cuenta/cambiar-clave')) ?>"
                           class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/cuenta/cambiar-clave', $currentPath) ?>">
                            <i class="fas fa-key me-2 opacity-75"></i> Cambiar contraseña
                        </a>

                        <a href="<?= htmlspecialchars(url('/logout')) ?>"
                           class="nav-link gp-admin-nav-link text-red-600 font-semibold mt-4 border border-red-600/25 rounded-lg">
                            <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
                        </a>
                    </nav>
                </div>
            </aside>
            <div class="gp-admin-content gp-vt-page w-full lg:w-3/4 xl:w-5/6 max-w-full shrink-0 px-3 mt-6">
