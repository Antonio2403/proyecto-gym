<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Spartum · Tu ritmo, tu meta</title>
    <meta name="csrf-token" content="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <?php require __DIR__ . '/../partials/session_idle_meta.php'; ?>

    <?php require __DIR__ . '/../partials/gp_page_transition_boot.php'; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Outfit:wght@500..800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= htmlspecialchars(asset('css/app-theme.css')) ?>">
</head>

<body class="site-body">

<?php
$gpClientePlanNombre = null;
if (isset($_SESSION['usuario_id']) && (string) ($_SESSION['rol'] ?? '') === 'cliente') {
    require_once __DIR__ . '/../../../modelos/cliente_subscripcion.php';
    $gpClientePlanNombre = ClienteSubscripcion::nombrePlanVisiblePorUsuario((int) $_SESSION['usuario_id']);
}

$gpNavbarBrandHref = url('/inicio');
$gpNavAvatarImg = 'https://ui-avatars.com/api/?name=' . rawurlencode((string) ($_SESSION['nombre'] ?? 'U')) . '&background=c45c28&color=fff&rounded=true&size=32';
?>

<nav class="navbar navbar-dark navbar-expand-lg site-navbar gp-vt-chrome sticky-top">
    <div class="container-fluid px-3 px-lg-4">

        <a class="navbar-brand" href="<?= htmlspecialchars($gpNavbarBrandHref) ?>">Spartum</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto align-items-lg-center gap-lg-1">
                <?php if (!isset($_SESSION['usuario_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/login')) ?>">Entrar</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="<?= htmlspecialchars(url('/contacto')) ?>">Contacto</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= htmlspecialchars(url('/quienes-somos')) ?>">Quiénes somos</a>
                </li>

                <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'fisio'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/fisio')) ?>">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/fisio/citas')) ?>">Citas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/fisio/citas/confirmadas')) ?>">Confirmadas</a>
                    </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'cliente'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/inicioUsuario')) ?>">Mi panel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/usuario/actividades')) ?>">Actividades</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/usuario/inscripciones/mis-inscripciones')) ?>">Mis reservas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/usuario/fisio')) ?>">Fisioterapia</a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'fisio'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= htmlspecialchars($gpNavAvatarImg) ?>"
                                 alt="" width="32" height="32" class="rounded-circle border border-secondary border-opacity-25">
                            <span class="d-none d-md-inline text-start">
                                <span class="fw-medium"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></span>
                                <span class="d-block small text-white-50">Fisioterapeuta</span>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg py-2">
                            <li>
                                <a class="dropdown-item text-danger" href="<?= htmlspecialchars(url('/logout')) ?>">Cerrar sesión</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['usuario_id']) && in_array((string) ($_SESSION['rol'] ?? ''), ['admin', 'monitor'], true)): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= htmlspecialchars($gpNavAvatarImg) ?>"
                                 alt="" width="32" height="32" class="rounded-circle border border-secondary border-opacity-25">
                            <span class="d-none d-md-inline text-start">
                                <span class="d-inline-flex flex-wrap align-items-center gap-2">
                                    <span class="fw-medium"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></span>
                                    <span class="gp-navbar-plan gp-navbar-plan--role rounded-pill"><?= ($_SESSION['rol'] ?? '') === 'admin' ? 'Admin' : 'Monitor' ?></span>
                                </span>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg py-2">
                            <li>
                                <a class="dropdown-item" href="<?= htmlspecialchars(($_SESSION['rol'] ?? '') === 'admin' ? url('/admin') : url('/inicioMonitor')) ?>">Ir al panel</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= htmlspecialchars(url('/cuenta/perfil')) ?>">Mi perfil</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= htmlspecialchars(url('/cuenta/cambiar-clave')) ?>">Cambiar contraseña</a>
                            </li>
                            <li><hr class="dropdown-divider opacity-25"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= htmlspecialchars(url('/logout')) ?>">Cerrar sesión</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'cliente'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= htmlspecialchars($gpNavAvatarImg) ?>"
                                 alt="" width="32" height="32" class="rounded-circle border border-secondary border-opacity-25">
                            <span class="d-none d-md-inline text-start">
                                <span class="d-inline-flex flex-wrap align-items-center gap-2">
                                    <span class="fw-medium"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></span>
                                    <?php if ($gpClientePlanNombre): ?>
                                        <span class="gp-navbar-plan rounded-pill"><?= htmlspecialchars($gpClientePlanNombre) ?></span>
                                    <?php endif; ?>
                                </span>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg py-2">
                            <?php if ($gpClientePlanNombre): ?>
                                <li class="px-3 py-2 small">
                                    <span class="text-muted d-block mb-1">Plan activo</span>
                                    <span class="fw-semibold"><?= htmlspecialchars($gpClientePlanNombre) ?></span>
                                </li>
                                <li><hr class="dropdown-divider opacity-25"></li>
                            <?php endif; ?>
                            <li>
                                <a class="dropdown-item" href="<?= htmlspecialchars(url('/clientes/editar')) ?>">Mi perfil</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= htmlspecialchars(url('/pago')) ?>">Suscripciones</a>
                            </li>
                            <li><hr class="dropdown-divider opacity-25"></li>
                            <li>
                                <a class="dropdown-item" href="<?= htmlspecialchars(url('/darse-de-baja')) ?>">Darse de baja</a>
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= htmlspecialchars(url('/logout')) ?>">Cerrar sesión</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (!isset($_SESSION['usuario_id'])): ?>
                    <li class="nav-item d-flex align-items-center">
                        <a href="<?= htmlspecialchars(url('/pago')) ?>" class="btn gp-navbar-cta">Planes</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</nav>

<?php require __DIR__ . '/../partials/gp_flash_banner.php'; ?>

<main class="site-main gp-vt-page">
