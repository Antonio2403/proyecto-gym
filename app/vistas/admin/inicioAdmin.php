<div class="content-wrapper gp-dash">
    <div class="container-fluid">
        <header class="gp-dash-hero">
            <span class="gp-badge d-inline-block mb-2">Panel</span>
            <h1 class="h3 mb-0">Administración</h1>
            <p class="mb-0 mt-2">Accesos rápidos al centro. Elige un módulo para gestionar.</p>
        </header>

        <nav class="gp-bento gp-bento--admin gp-bento--admin-grid" aria-label="Menú de administración">
            <a href="<?= htmlspecialchars(url('/admin/verClientes')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-users"></i></span>
                <span class="gp-bento-tile__label">Clientes</span>
                <span class="gp-bento-tile__desc">Altas, planes y accesos</span>
            </a>
            <a href="<?= htmlspecialchars(url('/admin/verMonitores')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-user-tie"></i></span>
                <span class="gp-bento-tile__label">Monitores</span>
                <span class="gp-bento-tile__desc">Equipo y especialidades</span>
            </a>
            <a href="<?= htmlspecialchars(url('/admin/fisioterapeutas')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-heart-pulse"></i></span>
                <span class="gp-bento-tile__label">Fisioterapeutas</span>
                <span class="gp-bento-tile__desc">Profesionales y citas</span>
            </a>
            <a href="<?= htmlspecialchars(url('/admin/gestionSubscripciones')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-credit-card"></i></span>
                <span class="gp-bento-tile__label">Suscripciones</span>
                <span class="gp-bento-tile__desc">Planes y ofertas</span>
            </a>
            <a href="<?= htmlspecialchars(url('/admin/verSolicitudes')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-envelope"></i></span>
                <span class="gp-bento-tile__label">Solicitudes</span>
                <span class="gp-bento-tile__desc">Peticiones de monitores</span>
            </a>
            <a href="<?= htmlspecialchars(url('/admin/recuperacion-cuenta')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-ticket"></i></span>
                <span class="gp-bento-tile__label">Tickets de cuenta</span>
                <span class="gp-bento-tile__desc">Reactivación de accesos</span>
            </a>
            <a href="<?= htmlspecialchars(url('/admin/feedback')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-comment-dots"></i></span>
                <span class="gp-bento-tile__label">Feedback contacto</span>
                <span class="gp-bento-tile__desc">Mensajes del formulario</span>
            </a>
            <a href="<?= htmlspecialchars(url('/admin/gestionarActividades')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-calendar-alt"></i></span>
                <span class="gp-bento-tile__label">Actividades</span>
                <span class="gp-bento-tile__desc">Horarios y clases</span>
            </a>
            <a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-dumbbell"></i></span>
                <span class="gp-bento-tile__label">Salas</span>
                <span class="gp-bento-tile__desc">Espacios y capacidad</span>
            </a>
            <a href="<?= htmlspecialchars(url('/admin/config-seguridad')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-shield-halved"></i></span>
                <span class="gp-bento-tile__label">Seguridad</span>
                <span class="gp-bento-tile__desc">Claves e inactividad</span>
            </a>
        </nav>
    </div>
</div>
