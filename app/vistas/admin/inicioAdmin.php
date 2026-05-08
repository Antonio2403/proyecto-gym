<div class="content-wrapper">
    <div class="container-fluid">
        <div class="gp-admin-card-panel mb-4">
            <h1 class="h3 mb-2">Administración</h1>
            <p class="text-muted mb-0">Accesos rápidos al panel Clara / Spartum.</p>
        </div>

        <div class="row g-3">
            <div class="col-sm-6 col-xl-4">
                <a href="<?= htmlspecialchars(url('/admin/verClientes')) ?>" class="text-decoration-none d-block gp-admin-card-panel h-100">
                    <div class="fw-semibold text-dark"><i class="fas fa-users me-2 text-primary"></i>Clientes</div>
                    <div class="small text-muted mt-1">Listado de usuarios registrados</div>
                </a>
            </div>
            <div class="col-sm-6 col-xl-4">
                <a href="<?= htmlspecialchars(url('/admin/fisioterapeutas')) ?>" class="text-decoration-none d-block gp-admin-card-panel h-100">
                    <div class="fw-semibold text-dark"><i class="fas fa-heart-pulse me-2 text-primary"></i>Fisioterapeutas</div>
                    <div class="small text-muted mt-1">Altas y edición de fisios</div>
                </a>
            </div>
            <div class="col-sm-6 col-xl-4">
                <a href="<?= htmlspecialchars(url('/admin/gestionarActividades')) ?>" class="text-decoration-none d-block gp-admin-card-panel h-100">
                    <div class="fw-semibold text-dark"><i class="fas fa-calendar-alt me-2 text-primary"></i>Actividades</div>
                    <div class="small text-muted mt-1">Sesiones y horarios</div>
                </a>
            </div>
            <div class="col-sm-6 col-xl-4">
                <a href="<?= htmlspecialchars(url('/admin/gestionSubscripciones')) ?>" class="text-decoration-none d-block gp-admin-card-panel h-100">
                    <div class="fw-semibold text-dark"><i class="fas fa-credit-card me-2 text-primary"></i>Suscripciones</div>
                    <div class="small text-muted mt-1">Planes y precios</div>
                </a>
            </div>
            <div class="col-sm-6 col-xl-4">
                <a href="<?= htmlspecialchars(url('/admin/verMonitores')) ?>" class="text-decoration-none d-block gp-admin-card-panel h-100">
                    <div class="fw-semibold text-dark"><i class="fas fa-user-tie me-2 text-primary"></i>Monitores</div>
                    <div class="small text-muted mt-1">Equipo del centro</div>
                </a>
            </div>
            <div class="col-sm-6 col-xl-4">
                <a href="<?= htmlspecialchars(url('/admin/verSolicitudes')) ?>" class="text-decoration-none d-block gp-admin-card-panel h-100">
                    <div class="fw-semibold text-dark"><i class="fas fa-envelope me-2 text-primary"></i>Solicitudes</div>
                    <div class="small text-muted mt-1">Peticiones de monitores</div>
                </a>
            </div>
            <div class="col-sm-6 col-xl-4">
                <a href="<?= htmlspecialchars(url('/admin/feedback')) ?>" class="text-decoration-none d-block gp-admin-card-panel h-100">
                    <div class="fw-semibold text-dark"><i class="fas fa-comment-dots me-2 text-primary"></i>Feedback de contacto</div>
                    <div class="small text-muted mt-1">Mensajes del formulario web</div>
                </a>
            </div>
        </div>
    </div>
</div>
