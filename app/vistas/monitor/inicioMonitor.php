<div class="content-wrapper gp-dash">
    <div class="relative w-full px-3">
        <header class="gp-dash-hero">
            <span class="gp-badge inline-block mb-2">Monitor</span>
            <h1 class="text-[1.75rem] mb-0">Panel de monitor</h1>
            <p class="mb-0 mt-2">
                Hola, <?= htmlspecialchars($_SESSION['nombre'] ?? 'Monitor') ?>.
                Accesos rápidos a tus reportes y a las salas del centro.
            </p>
        </header>

        <nav class="gp-bento gp-bento--admin gp-bento--monitor-grid" aria-label="Menú de monitor">
            <a href="<?= htmlspecialchars(url('/monitor/mis-clases')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-calendar-check"></i></span>
                <span class="gp-bento-tile__label">Mis clases</span>
                <span class="gp-bento-tile__desc">Alumnos inscritos en tus sesiones</span>
            </a>
            <a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-dumbbell"></i></span>
                <span class="gp-bento-tile__label">Salas</span>
                <span class="gp-bento-tile__desc">Espacios y materiales</span>
            </a>
            <a href="<?= htmlspecialchars(url('/monitor/formSolicitud')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-plus-circle"></i></span>
                <span class="gp-bento-tile__label">Reportes</span>
                <span class="gp-bento-tile__desc">Crear un reporte nuevo</span>
            </a>
            <a href="<?= htmlspecialchars(url('/monitor/verMisSolicitudes')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-list"></i></span>
                <span class="gp-bento-tile__label">Tus reportes</span>
                <span class="gp-bento-tile__desc">Solo los que has enviado tú</span>
            </a>
        </nav>
    </div>
</div>
