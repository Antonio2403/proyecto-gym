<?php
$rolPanel = $_SESSION['rol'] ?? '';
$esAdmin = ($rolPanel === 'admin');
?>
<nav class="navbar navbar-expand-lg gp-admin-topnav gp-vt-chrome sticky-top">
    <div class="container-fluid px-3 px-lg-4">
        <a class="navbar-brand text-white" href="<?= htmlspecialchars(url('/inicio')) ?>">Spartum</a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#adminTopNav" aria-controls="adminTopNav" aria-expanded="false" aria-label="Menú superior">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminTopNav">
            <ul class="navbar-nav mx-auto gap-lg-2 align-items-lg-center">
                <?php if ($esAdmin): ?>
                    <li class="nav-item d-lg-none">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/admin/verClientes')) ?>">Clientes</a>
                    </li>
                    <li class="nav-item d-lg-none">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/admin/fisioterapeutas')) ?>">Fisios</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item d-lg-none">
                    <a class="nav-link" href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>">Salas</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto align-items-lg-center flex-wrap gap-1">
                <?php if ($esAdmin): ?>
                    <li class="nav-item d-none d-xl-block">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/admin/verClientes')) ?>">Clientes</a>
                    </li>
                    <li class="nav-item d-none d-xl-block">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/admin/fisioterapeutas')) ?>">Fisioterapeutas</a>
                    </li>
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/admin/gestionarActividades')) ?>">Actividades</a>
                    </li>
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/admin/gestionSubscripciones')) ?>">Suscripciones</a>
                    </li>
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/admin/feedback')) ?>">Feedback</a>
                    </li>
                    <li class="nav-item d-lg-none">
                        <a class="nav-link" href="<?= htmlspecialchars(url('/admin/feedback')) ?>">Feedback</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="btn btn-outline-light btn-sm fw-semibold px-3 ms-lg-2"
                       href="<?= htmlspecialchars(url('/logout')) ?>">Salir</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php require __DIR__ . '/../partials/gp_flash_banner.php'; ?>
