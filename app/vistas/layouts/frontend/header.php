<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Spartum · Tu ritmo, tu meta</title>
    <?php require __DIR__ . '/../partials/favicon.php'; ?>
    <meta name="csrf-token" content="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <?php require __DIR__ . '/../partials/session_idle_meta.php'; ?>

    <?php require __DIR__ . '/../partials/gp_page_transition_boot.php'; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Outfit:wght@500..800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= htmlspecialchars(asset('css/tailwind.css')) ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= htmlspecialchars(asset('css/app-theme.css')) ?>?v=20261027a">
    <!-- También en la página: si el navegador conserva el CSS anterior, la portada vuelve al diseño viejo. -->
    <style>
        .site-body .btn { border-radius: 999px; }
        .gp-photo-frame { width: 100%; overflow: hidden; }
        .gp-photo { display: block; width: 100%; height: 100%; max-width: none; object-fit: cover; object-position: center; }
        .gp-photo-strip { display: grid; grid-template-columns: 1fr; gap: 1rem; align-items: stretch; }
        .gp-photo-card { min-width: 0; margin: 0; }
        .gp-photo-card .gp-photo-frame { width: 100%; height: 14rem; }
        .gp-hero--poster { display: flex; flex-direction: column; min-height: calc(100svh - 4.25rem); padding: 0; background: #2c3544; border-bottom: 0; overflow: hidden; }
        .gp-hero-poster__stage { position: relative; flex: 1 1 auto; display: flex; align-items: center; min-height: 28rem; background: #2c3544; }
        .gp-hero-poster__frame { position: absolute; top: 0; right: 0; width: 48%; height: 100%; overflow: hidden; }
        .gp-hero-poster__photo { display: block; width: 100%; height: 100%; max-width: none; object-fit: cover; object-position: center 14%; }
        .gp-hero-poster__shade { position: absolute; inset: 0; background: linear-gradient(90deg, #2c3544 0%, #2c3544 42%, rgba(44, 53, 68, 0.88) 56%, rgba(44, 53, 68, 0.28) 70%, rgba(44, 53, 68, 0) 82%); pointer-events: none; }
        .gp-hero-poster__copy { position: relative; z-index: 1; width: min(52rem, 62%); padding: clamp(2.25rem, 5vw, 4.5rem) clamp(1.35rem, 3.2vw, 3rem); }
        .site-body .gp-hero--poster .gp-hero-title { margin-bottom: 1.35rem; color: #fff; font-size: clamp(3.8rem, 6.6vw, 6.35rem); font-weight: 800; line-height: 0.92; letter-spacing: -0.045em; }
        .site-body .gp-hero--poster .gp-hero-lead { max-width: 34rem; margin-bottom: 1.85rem; color: rgba(255, 255, 255, 0.92); font-size: clamp(1.2rem, 1.85vw, 1.55rem); line-height: 1.45; }
        .gp-hero-poster__actions { display: flex; flex-wrap: wrap; gap: 0.85rem; }
        .gp-hero--poster .btn { border-radius: 0.8rem; }
        .gp-hero--poster .btn-outline-light { border: 1px solid rgba(255, 255, 255, 0.85); background: transparent; color: #fff; }
        .gp-hero-poster__strip { background: #fff; }
        .gp-hero-poster__points { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); width: min(100%, 80rem); margin: 0 auto; padding: 1.15rem 1.25rem; list-style: none; text-align: center; }
        .gp-hero-poster__points li { color: #2c3544; font-weight: 600; }
        .gp-hero-poster__points a { color: #c45c28; font-weight: 700; text-decoration: none; }
        @media (min-width: 768px) {
            .gp-photo-strip { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @media (max-width: 860px) {
            .gp-hero--poster { min-height: 0; }
            .gp-hero-poster__stage { flex: 0 0 auto; flex-direction: column; align-items: stretch; min-height: 0; }
            .gp-hero-poster__frame { position: relative; order: 2; top: auto; right: auto; flex: 0 0 auto; width: 100%; height: min(28rem, 118vw); overflow: hidden; }
            .gp-hero-poster__photo { position: absolute; top: -24%; left: -86%; width: 227%; height: auto; max-width: none; }
            .gp-hero-poster__shade { display: none; }
            .gp-hero-poster__copy { order: 1; width: 100%; padding: 2.6rem 1.4rem 2.6rem; }
            .site-body .gp-hero--poster .gp-hero-title { font-size: clamp(2.95rem, 13vw, 3.85rem); line-height: 1.08; margin-bottom: 1.25rem; }
            .site-body .gp-hero--poster .gp-hero-lead { font-size: 1.2rem; line-height: 1.5; margin-bottom: 1.5rem; }
            .gp-hero-poster__points { grid-template-columns: 1fr 1fr; gap: 0.95rem 0.65rem; }
        }
    </style>
    <?php require __DIR__ . '/../partials/horario_centro_boot.php'; ?>
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

<nav class="navbar navbar-dark navbar-expand-lg site-navbar gp-vt-chrome sticky top-0 z-[1020]">
    <div class="relative w-full px-4 lg:px-6">

        <a class="navbar-brand" href="<?= htmlspecialchars($gpNavbarBrandHref) ?>">Spartum</a>

        <button class="navbar-toggler" type="button" data-gp-toggle="collapse" data-gp-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto lg:items-center lg:gap-1">
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
                <?php if (isset($_SESSION['usuario_id']) && in_array((string) ($_SESSION['rol'] ?? ''), ['admin', 'monitor'], true)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(($_SESSION['rol'] ?? '') === 'admin' ? url('/admin') : url('/inicioMonitor')) ?>">Panel</a>
                    </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'cliente'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/inicioUsuario')) ?>">Mi panel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/usuario/actividades')) ?>">Clases</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/usuario/inscripciones/mis-inscripciones')) ?>">Mis reservas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/pago')) ?>">Planes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/usuario/fisio')) ?>">Fisioterapia</a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto lg:items-center gap-2">
                <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'fisio'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle flex items-center gap-2" href="#" role="button"
                           data-gp-toggle="dropdown" aria-expanded="false">
                            <img src="<?= htmlspecialchars($gpNavAvatarImg) ?>"
                                 alt="" width="32" height="32" class="rounded-full border border-slate-500/25">
                            <span class="hidden md:inline text-left">
                                <span class="font-medium"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></span>
                                <span class="block text-sm text-white/50">Fisioterapeuta</span>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg py-2">
                            <li>
                                <a class="dropdown-item text-red-600" href="<?= htmlspecialchars(url('/logout')) ?>">Cerrar sesión</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['usuario_id']) && in_array((string) ($_SESSION['rol'] ?? ''), ['admin', 'monitor'], true)): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle flex items-center gap-2" href="#" role="button"
                           data-gp-toggle="dropdown" aria-expanded="false">
                            <img src="<?= htmlspecialchars($gpNavAvatarImg) ?>"
                                 alt="" width="32" height="32" class="rounded-full border border-slate-500/25">
                            <span class="hidden md:inline text-left">
                                <span class="inline-flex flex-wrap items-center gap-2">
                                    <span class="font-medium"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></span>
                                    <span class="gp-navbar-plan gp-navbar-plan--role rounded-full"><?= ($_SESSION['rol'] ?? '') === 'admin' ? 'Admin' : 'Monitor' ?></span>
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
                                <a class="dropdown-item text-red-600" href="<?= htmlspecialchars(url('/logout')) ?>">Cerrar sesión</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'cliente'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle flex items-center gap-2" href="#" role="button"
                           data-gp-toggle="dropdown" aria-expanded="false">
                            <img src="<?= htmlspecialchars($gpNavAvatarImg) ?>"
                                 alt="" width="32" height="32" class="rounded-full border border-slate-500/25">
                            <span class="hidden md:inline text-left">
                                <span class="inline-flex flex-wrap items-center gap-2">
                                    <span class="font-medium"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></span>
                                    <?php if ($gpClientePlanNombre): ?>
                                        <span class="gp-navbar-plan rounded-full"><?= htmlspecialchars($gpClientePlanNombre) ?></span>
                                    <?php endif; ?>
                                </span>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg py-2">
                            <?php if ($gpClientePlanNombre): ?>
                                <li class="px-4 py-2 text-sm">
                                    <span class="text-slate-500 block mb-1">Plan activo</span>
                                    <span class="font-semibold"><?= htmlspecialchars($gpClientePlanNombre) ?></span>
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
                                <a class="dropdown-item text-red-600" href="<?= htmlspecialchars(url('/logout')) ?>">Cerrar sesión</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (!isset($_SESSION['usuario_id'])): ?>
                    <li class="nav-item flex items-center">
                        <a href="<?= htmlspecialchars(url('/pago')) ?>" class="btn gp-navbar-cta">Planes</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</nav>

<?php require __DIR__ . '/../partials/gp_flash_banner.php'; ?>

<main class="site-main gp-vt-page">
