<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <!-- Logo -->
    <a href="/proyecto-gym" class="brand-link text-center">
        <span class="brand-text font-weight-light">GYM PANEL</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Usuario -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block">
                    <?= $_SESSION['usuario'] ?? 'Usuario' ?>
                </a>
                <small class="text-muted">
                    <?= $_SESSION['rol'] ?? '' ?>
                </small>
            </div>
        </div>

        <!-- Menú -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">

                <!-- DASHBOARD -->
                <li class="nav-item">
                    <a href="/proyecto-gym/admin" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- ADMIN -->
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>

                    <li class="nav-header">ADMIN</li>

                    <li class="nav-item">
                        <a href="/proyecto-gym/admin/usuarios" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Usuarios</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/proyecto-gym/admin/monitores" class="nav-link">
                            <i class="nav-icon fas fa-user-tie"></i>
                            <p>Monitores</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/proyecto-gym/admin/suscripciones" class="nav-link">
                            <i class="nav-icon fas fa-credit-card"></i>
                            <p>Suscripciones</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/proyecto-gym/admin/solicitudes" class="nav-link">
                            <i class="nav-icon fas fa-envelope"></i>
                            <p>Solicitudes</p>
                        </a>
                    </li>

                <?php endif; ?>

                <!-- MONITOR -->
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'monitor'): ?>

                    <li class="nav-header">MONITOR</li>

                    <li class="nav-item">
                        <a href="/proyecto-gym/monitor/solicitudes" class="nav-link">
                            <i class="nav-icon fas fa-envelope-open"></i>
                            <p>Ver Solicitudes</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/proyecto-gym/monitor/mis-solicitudes" class="nav-link">
                            <i class="nav-icon fas fa-list"></i>
                            <p>Mis Solicitudes</p>
                        </a>
                    </li>

                <?php endif; ?>

                <!-- COMÚN -->
                <li class="nav-header">GENERAL</li>

                <li class="nav-item">
                    <a href="/proyecto-gym/salas" class="nav-link">
                        <i class="nav-icon fas fa-dumbbell"></i>
                        <p>Salas</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/proyecto-gym/logout" class="nav-link text-danger">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Cerrar sesión</p>
                    </a>
                </li>

            </ul>
        </nav>

    </div>
</aside>