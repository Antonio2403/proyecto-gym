<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gym App</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tu CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>

<nav class="navbar navbar-dark bg-dark navbar-expand-lg">
    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="/proyecto-gym/inicio">GYM</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <!-- SI NO ESTÁ LOGUEADO -->
                <?php if (!isset($_SESSION['usuario_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/proyecto-gym/login">Login</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="/proyecto-gym/contacto">Contacto</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/proyecto-gym/quienes-somos">Quiénes somos</a>
                </li>

                <!-- SOLO LOGUEADO -->
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/proyecto-gym/actividades">Actividades</a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <!-- MENÚ DE USUARIO LOGUEADO -->
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nombre']) ?>&background=random" alt="Avatar" style="width: 24px; height: 24px; border-radius: 50%; margin-right: 5px;"> <?= $_SESSION['nombre'] ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="/proyecto-gym/clientes/editar">
                                    Modificar perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/proyecto-gym/pago">
                                    Suscripciones
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="/proyecto-gym/darse-de-baja">
                                    Darse de baja
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="/proyecto-gym/logout">
                                    Cerrar sesión
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="/proyecto-gym/pago" class="btn btn-warning btn-sm ms-2">
                            Suscripciones
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</nav>

<div class="container mt-4">