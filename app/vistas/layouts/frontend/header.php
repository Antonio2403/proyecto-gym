<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Spartum · Tu ritmo, tu meta</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Outfit:wght@500..800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
if (isset($_SESSION['usuario_id'])) {
    $gpRolNavbar = (string) ($_SESSION['rol'] ?? '');
    if ($gpRolNavbar === 'monitor') {
        $gpNavbarBrandHref = url('/inicioMonitor');
    } elseif ($gpRolNavbar === 'fisio') {
        $gpNavbarBrandHref = url('/fisio');
    } elseif ($gpRolNavbar === 'cliente') {
        $gpNavbarBrandHref = url('/inicioUsuario');
    }
}
?>

<nav class="navbar navbar-dark navbar-expand-lg site-navbar sticky-top">
    <div class="container-fluid px-lg-5">

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
                <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') !== 'fisio'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/usuario/actividades')) ?>">Actividades</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/usuario/inscripciones/mis-inscripciones')) ?>">Mis reservas</a>
                    </li>
                    <?php if (($_SESSION['rol'] ?? '') === 'cliente'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= htmlspecialchars(url('/usuario/fisio')) ?>">Fisioterapia</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'fisio'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nombre'] ?? 'U') ?>&background=c45c28&color=fff&rounded=true&size=32"
                                 alt="" width="32" height="32" class="rounded-circle border border-secondary border-opacity-25">
                            <span class="d-none d-md-inline text-start">
                                <span class="fw-medium"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></span>
                                <span class="d-block small text-white-50">Fisioterapeuta</span>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg py-2">
                            <li>
                                <a class="dropdown-item text-danger" href="<?= htmlspecialchars(url('/logout')) ?>"
                                   data-gp-confirm
                                   data-gp-confirm-title="Cerrar sesión"
                                   data-gp-confirm-body="¿Salir de tu cuenta? Se cerrará la sesión actual."
                                   data-gp-confirm-ok="Sí, salir">Cerrar sesión</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') !== 'fisio'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nombre'] ?? 'U') ?>&background=c45c28&color=fff&rounded=true&size=32"
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
                                <a class="dropdown-item" href="<?= htmlspecialchars(url('/darse-de-baja')) ?>"
                                   data-gp-confirm
                                   data-gp-confirm-title="Darte de baja"
                                   data-gp-confirm-body="Vas a abrir la información sobre baja de membresía y contacto con el centro. ¿Continuar?"
                                   data-gp-confirm-ok="Sí, continuar">Darse de baja</a>
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= htmlspecialchars(url('/logout')) ?>"
                                   data-gp-confirm
                                   data-gp-confirm-title="Cerrar sesión"
                                   data-gp-confirm-body="¿Salir de tu cuenta Spartum?"
                                   data-gp-confirm-ok="Sí, salir">Cerrar sesión</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (!isset($_SESSION['usuario_id'])): ?>
                    <li class="nav-item">
                        <a href="<?= htmlspecialchars(url('/pago')) ?>" class="btn btn-warning btn-sm px-3">Planes</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</nav>

<main class="site-main">
