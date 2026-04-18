<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <!-- Logo -->
    <a href="/proyecto-gym" class="brand-link text-center">
        <span class="brand-text font-weight-bold fs-5">GYM PANEL</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Usuario -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
                <i class="fas fa-user-tie img-circle elevation-2" style="font-size: 36px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background-color: #f0f0f0; border-radius: 50%;"></i>
            <div class="info">
                <a href="#" class="d-block fw-bold">
                    <?= $_SESSION['usuario'] ?? 'Usuario' ?>
                </a>
                <small class="text-muted d-block">
                    <span class="badge bg-success"><?= $_SESSION['rol'] ?? 'N/A' ?></span>
                </small>
            </div>
        </div>

        <!-- Menú -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column gap-2" data-widget="treeview">

                <!-- DASHBOARD -->
                <li class="nav-item">
                    <a href="/proyecto-gym/admin" class="nav-link rounded">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- ADMIN -->
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <li class="nav-header text-uppercase fw-bold mt-3">ADMIN</li>
                    
                    <li class="nav-item">
                        <a href="/proyecto-gym/admin/usuarios" class="nav-link rounded">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Usuarios</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/proyecto-gym/admin/monitores" class="nav-link rounded">
                            <i class="nav-icon fas fa-user-tie"></i>
                            <p>Monitores</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/proyecto-gym/admin/suscripciones" class="nav-link rounded">
                            <i class="nav-icon fas fa-credit-card"></i>
                            <p>Suscripciones</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/proyecto-gym/admin/solicitudes" class="nav-link rounded">
                            <i class="nav-icon fas fa-envelope"></i>
                            <p>Solicitudes</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- MONITOR -->
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'monitor'): ?>
                    <li class="nav-header text-uppercase fw-bold mt-3">MONITOR</li>

                    <li class="nav-item">
                        <a href="/proyecto-gym/monitor/solicitudes" class="nav-link rounded">
                            <i class="nav-icon fas fa-envelope-open"></i>
                            <p>Ver Solicitudes</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/proyecto-gym/monitor/mis-solicitudes" class="nav-link rounded">
                            <i class="nav-icon fas fa-list"></i>
                            <p>Mis Solicitudes</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- GENERAL -->
                <li class="nav-header text-uppercase fw-bold mt-3">GENERAL</li>

                <li class="nav-item">
                    <a href="/proyecto-gym/salas" class="nav-link rounded">
                        <i class="nav-icon fas fa-dumbbell"></i>
                        <p>Salas</p>
                    </a>
                </li>

                <li class="nav-item mt-4">
                    <a href="/proyecto-gym/logout" class="nav-link rounded bg-danger text-white">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Cerrar sesión</p>
                    </a>
                </li>

            </ul>
        </nav>

    </div>
</aside>
