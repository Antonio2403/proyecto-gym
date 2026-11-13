<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <?php require __DIR__ . '/../partials/gp_cookie_boot.php'; ?>
    <?php
    require_once dirname(__DIR__, 4) . '/core/helpers/seo.php';
    $gpSeo = gp_seo_actual();
    ?>
    <title><?= htmlspecialchars($gpSeo['title'], ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($gpSeo['description'], ENT_QUOTES, 'UTF-8') ?>">
    <meta name="robots" content="<?= htmlspecialchars($gpSeo['robots'], ENT_QUOTES, 'UTF-8') ?>">
    <link rel="canonical" href="<?= htmlspecialchars($gpSeo['canonical'], ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:locale" content="es_ES">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Spartum">
    <meta property="og:title" content="<?= htmlspecialchars($gpSeo['title'], ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($gpSeo['description'], ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($gpSeo['canonical'], ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($gpSeo['robots'] === 'index, follow'): ?>
    <meta property="og:image" content="<?= htmlspecialchars($gpSeo['image'], ENT_QUOTES, 'UTF-8') ?>">
    <script type="application/ld+json"><?= gp_seo_json_ld() ?></script>
    <?php endif; ?>
    <?php require __DIR__ . '/../partials/favicon.php'; ?>
    <meta name="csrf-token" content="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <?php require __DIR__ . '/../partials/session_idle_meta.php'; ?>

    <?php require __DIR__ . '/../partials/gp_page_transition_boot.php'; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Outfit:wght@500..800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= htmlspecialchars(asset('css/tailwind.css')) ?>?v=20261029h">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= htmlspecialchars(asset('css/app-theme.css')) ?>?v=20261113f">
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
$gpCurrentPath = strtok((string) ($_SERVER['REQUEST_URI'] ?? '/'), '?') ?: '/';
$gpNavCurrent = static fn(string $path): bool => $gpCurrentPath === url($path);
?>

<nav class="navbar navbar-dark navbar-expand-lg site-navbar gp-vt-chrome sticky top-0 z-[1020]">
    <div class="relative w-full px-4 lg:px-6">

        <button class="navbar-toggler" type="button" data-gp-toggle="collapse" data-gp-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a class="navbar-brand" href="<?= htmlspecialchars($gpNavbarBrandHref) ?>">
            <img src="<?= htmlspecialchars(asset('img/logo-spartum.png')) ?>?v=20261112a" alt="Spartum Gym" width="220" height="80">
        </a>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto lg:items-center lg:gap-1">
                <?php if (!isset($_SESSION['usuario_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $gpNavCurrent('/usuario/actividades') ? ' active' : '' ?>"
                           <?= $gpNavCurrent('/usuario/actividades') ? 'aria-current="page"' : '' ?>
                           href="<?= htmlspecialchars(url('/usuario/actividades')) ?>">Horario</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link<?= $gpNavCurrent('/quienes-somos') ? ' active' : '' ?>"
                       <?= $gpNavCurrent('/quienes-somos') ? 'aria-current="page"' : '' ?>
                       href="<?= htmlspecialchars(url('/quienes-somos')) ?>">Quiénes somos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $gpNavCurrent('/contacto') ? ' active' : '' ?>"
                       <?= $gpNavCurrent('/contacto') ? 'aria-current="page"' : '' ?>
                       href="<?= htmlspecialchars(url('/contacto')) ?>">Contacto</a>
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
                        <a class="nav-link" href="<?= htmlspecialchars(url('/inicioUsuario')) ?>">Tu panel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/usuario/actividades')) ?>">Ver tu horario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/usuario/inscripciones/mis-inscripciones')) ?>">Ver tus reservas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/pago')) ?>">Ver tu plan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/usuario/fisio')) ?>">Ver tus citas</a>
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
                                <a class="dropdown-item" href="<?= htmlspecialchars(url('/clientes/editar')) ?>">Cambiar tus datos</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= htmlspecialchars(url('/pago')) ?>">Ver tu plan</a>
                            </li>
                            <li><hr class="dropdown-divider gp-dropdown-split"></li>
                            <li>
                                <a class="dropdown-item gp-dropdown-leave" href="<?= htmlspecialchars(url('/darse-de-baja')) ?>">Darse de baja</a>
                            </li>
                            <li>
                                <a class="dropdown-item gp-dropdown-leave" href="<?= htmlspecialchars(url('/logout')) ?>">Cerrar sesión</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (!isset($_SESSION['usuario_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $gpNavCurrent('/login') ? ' active' : '' ?>"
                           <?= $gpNavCurrent('/login') ? 'aria-current="page"' : '' ?>
                           href="<?= htmlspecialchars(url('/login')) ?>">Entrar</a>
                    </li>
                    <li class="nav-item flex items-center">
                        <a href="<?= htmlspecialchars(url('/login')) ?>?modo=registro" class="btn gp-navbar-cta">Hazte socio</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <button type="button" class="gp-nav-backdrop" data-gp-nav-backdrop hidden aria-label="Cerrar menú"></button>
    </div>
</nav>

<?php require __DIR__ . '/../partials/gp_flash_banner.php'; ?>

<main class="site-main gp-vt-page">
