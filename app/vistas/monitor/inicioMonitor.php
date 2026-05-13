<div class="content-wrapper gp-dash">
    <div class="container-fluid">
        <header class="gp-dash-hero">
            <span class="gp-badge d-inline-block mb-2">Monitor</span>
            <h1 class="h3 mb-0">Panel de monitor</h1>
            <p class="mb-0 mt-2">
                Hola, <?= htmlspecialchars($_SESSION['nombre'] ?? 'Monitor') ?>.
                Accesos rápidos a solicitudes y salas del centro.
            </p>
        </header>

        <nav class="gp-bento gp-bento--admin gp-bento--monitor-grid" aria-label="Menú de monitor">
            <a href="<?= htmlspecialchars(url('/monitor/verMonitorSolicitudes')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-envelope-open"></i></span>
                <span class="gp-bento-tile__label">Solicitudes centro</span>
                <span class="gp-bento-tile__desc">Revisar peticiones del equipo</span>
            </a>
            <a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-dumbbell"></i></span>
                <span class="gp-bento-tile__label">Salas</span>
                <span class="gp-bento-tile__desc">Espacios y materiales</span>
            </a>
            <a href="<?= htmlspecialchars(url('/monitor/formSolicitud')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-plus-circle"></i></span>
                <span class="gp-bento-tile__label">Nueva solicitud</span>
                <span class="gp-bento-tile__desc">Enviar petición al centro</span>
            </a>
            <a href="<?= htmlspecialchars(url('/monitor/verMisSolicitudes')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-list"></i></span>
                <span class="gp-bento-tile__label">Mis solicitudes</span>
                <span class="gp-bento-tile__desc">Estado de tus envíos</span>
            </a>
        </nav>
    </div>
</div>
