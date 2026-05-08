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
                <div class="gp-admin-sidecard p-3 sticky-lg-top" style="top: 1rem;">
                    <div class="gp-admin-user-line">
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?></div>
                        <span class="badge rounded-pill mt-1 bg-secondary bg-opacity-25 text-secondary-emphasis">
                            <?= htmlspecialchars($rol) ?>
                        </span>
                    </div>

                    <nav class="nav flex-column gp-admin-nav gap-1 mt-2">
                        <a href="<?= htmlspecialchars($dashboardHref) ?>"
                           class="nav-link gp-admin-nav-link<?= $currentPath === '/admin' || $currentPath === '/inicioMonitor' ? ' active' : '' ?>">
                            <i class="fas fa-home me-2 text-opacity-75"></i> Inicio
                        </a>

                        <?php if ($rol === 'admin'): ?>
                            <div class="gp-admin-nav-heading">Administración</div>
                            <a href="<?= htmlspecialchars(url('/admin/verClientes')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/admin/verClientes', $currentPath) ?>">
                                <i class="fas fa-users me-2 text-opacity-75"></i> Clientes
                            </a>
                            <a href="<?= htmlspecialchars(url('/admin/verMonitores')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/admin/verMonitores', $currentPath) ?>">
                                <i class="fas fa-user-tie me-2 text-opacity-75"></i> Monitores
                            </a>
                            <a href="<?= htmlspecialchars(url('/admin/fisioterapeutas')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/admin/fisioterapeutas', $currentPath) ?>">
                                <i class="fas fa-heart-pulse me-2 text-opacity-75"></i> Fisioterapeutas
                            </a>
                            <a href="<?= htmlspecialchars(url('/admin/gestionSubscripciones')) ?>"
                               class="nav-link gp-admin-nav-link<?=
                                    gp_admin_nav_active('/admin/gestionSubscripciones', $currentPath)
                                    . gp_admin_nav_active('/admin/formSubscripcion', $currentPath)
                                    . gp_admin_nav_active('/admin/formEditarSubscripcion', $currentPath)
                                ?>">
                                <i class="fas fa-credit-card me-2 text-opacity-75"></i> Suscripciones
                            </a>
                            <a href="<?= htmlspecialchars(url('/admin/verSolicitudes')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/admin/verSolicitudes', $currentPath) ?>">
                                <i class="fas fa-envelope me-2 text-opacity-75"></i> Solicitudes
                            </a>
                            <a href="<?= htmlspecialchars(url('/admin/feedback')) ?>"
                               class="nav-link gp-admin-nav-link<?=
                                    gp_admin_nav_active('/admin/feedback', $currentPath)
                                ?>">
                                <i class="fas fa-comment-dots me-2 text-opacity-75"></i> Feedback contacto
                            </a>
                            <a href="<?= htmlspecialchars(url('/admin/gestionarActividades')) ?>"
                               class="nav-link gp-admin-nav-link<?=
                                    gp_admin_nav_active('/admin/gestionarActividades', $currentPath)
                                    . gp_admin_nav_active('/admin/actividades', $currentPath)
                                ?>">
                                <i class="fas fa-calendar-alt me-2 text-opacity-75"></i> Actividades
                            </a>
                        <?php endif; ?>

                        <?php if ($rol === 'monitor'): ?>
                            <div class="gp-admin-nav-heading">Monitor</div>
                            <a href="<?= htmlspecialchars(url('/monitor/verMonitorSolicitudes')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/monitor/verMonitorSolicitudes', $currentPath) ?>">
                                <i class="fas fa-envelope-open me-2 text-opacity-75"></i> Solicitudes centro
                            </a>
                            <a href="<?= htmlspecialchars(url('/monitor/formSolicitud')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/monitor/formSolicitud', $currentPath) ?>">
                                <i class="fas fa-plus-circle me-2 text-opacity-75"></i> Nueva solicitud
                            </a>
                            <a href="<?= htmlspecialchars(url('/monitor/verMisSolicitudes')) ?>"
                               class="nav-link gp-admin-nav-link<?= gp_admin_nav_active('/monitor/verMisSolicitudes', $currentPath) ?>">
                                <i class="fas fa-list me-2 text-opacity-75"></i> Mis solicitudes
                            </a>
                        <?php endif; ?>

                        <div class="gp-admin-nav-heading">Operaciones</div>
                        <a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>"
                           class="nav-link gp-admin-nav-link<?=
                                gp_admin_nav_active('/monitor/verSalas', $currentPath)
                                . gp_admin_nav_active('/monitor/salas', $currentPath)
                            ?>">
                            <i class="fas fa-dumbbell me-2 text-opacity-75"></i> Salas
                        </a>

                        <a href="<?= htmlspecialchars(url('/logout')) ?>"
                           data-gp-confirm
                           data-gp-confirm-title="Cerrar sesión"
                           data-gp-confirm-body="¿Salir del panel?"
                           data-gp-confirm-ok="Sí, salir"
                           class="nav-link gp-admin-nav-link text-danger fw-semibold mt-3 border border-danger border-opacity-25 rounded">
                            <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
                        </a>
                    </nav>
                </div>
            </aside>
            <div class="col-lg-9 col-xl-10 gp-admin-content">
