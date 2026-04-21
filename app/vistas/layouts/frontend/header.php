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
    <div class="container">

        <a class="navbar-brand" href="/proyecto-gym/inicio">GYM</a>

        <!-- SI NO ESTÁ LOGUEADO -->
        <?php if (!isset($_SESSION['usuario_id'])): ?>
            <a class="navbar-brand" href="/proyecto-gym/login">Login</a>
        <?php endif; ?>

        <a class="navbar-brand" href="/proyecto-gym/contacto">Contacto</a>
        <a class="navbar-brand" href="/proyecto-gym/quienes-somos">Quiénes somos</a>

        <!-- SOLO LOGUEADO -->
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <a class="navbar-brand" href="/proyecto-gym/actividades">Actividades</a>
        <?php endif; ?>

        <div class="ms-auto">

            <?php if (isset($_SESSION['usuario_id'])): ?>

                <span class="navbar-text text-white me-2">
                    Bienvenido, <?= $_SESSION['nombre'] ?>
                </span>

                <a href="/proyecto-gym/logout" class="btn btn-danger ms-2">
                    Logout
                </a>

            <?php endif; ?>

            <a href="/proyecto-gym/pago" class="btn btn-warning ms-2">
                Suscripciones
            </a>

        </div>

    </div>
</nav>

<div class="container mt-4">