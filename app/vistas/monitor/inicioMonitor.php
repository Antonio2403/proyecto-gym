<div class="content-wrapper">
    <div class="container-fluid">
        <div class="gp-admin-card-panel mb-4">
            <h1 class="h3 mb-2">Panel de monitor</h1>
            <p class="text-muted mb-0">
                Hola, <?= htmlspecialchars($_SESSION['nombre'] ?? 'Monitor') ?>.
                Desde aquí gestionas solicitudes al centro y las salas del gimnasio.
            </p>
        </div>

        <div class="row g-3">
            <div class="col-sm-6 col-xl-4">
                <a href="<?= htmlspecialchars(url('/monitor/verMonitorSolicitudes')) ?>" class="text-decoration-none d-block gp-admin-card-panel h-100 shadow-sm gp-admin-dash-card">
                    <div class="fw-semibold text-dark"><i class="fas fa-envelope-open me-2 text-primary"></i>Solicitudes del centro</div>
                    <div class="small text-muted mt-1">Revisa las peticiones abiertas por el equipo.</div>
                </a>
            </div>
            <div class="col-sm-6 col-xl-4">
                <a href="<?= htmlspecialchars(url('/monitor/formSolicitud')) ?>" class="text-decoration-none d-block gp-admin-card-panel h-100 shadow-sm gp-admin-dash-card">
                    <div class="fw-semibold text-dark"><i class="fas fa-plus-circle me-2 text-primary"></i>Nueva solicitud</div>
                    <div class="small text-muted mt-1">Envía una solicitud a administración.</div>
                </a>
            </div>
            <div class="col-sm-6 col-xl-4">
                <a href="<?= htmlspecialchars(url('/monitor/verMisSolicitudes')) ?>" class="text-decoration-none d-block gp-admin-card-panel h-100 shadow-sm gp-admin-dash-card">
                    <div class="fw-semibold text-dark"><i class="fas fa-list me-2 text-primary"></i>Mis solicitudes</div>
                    <div class="small text-muted mt-1">Historial de tus peticiones.</div>
                </a>
            </div>
            <div class="col-sm-6 col-xl-4">
                <a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>" class="text-decoration-none d-block gp-admin-card-panel h-100 shadow-sm gp-admin-dash-card">
                    <div class="fw-semibold text-dark"><i class="fas fa-dumbbell me-2 text-primary"></i>Salas</div>
                    <div class="small text-muted mt-1">Gestionar salas y materiales.</div>
                </a>
            </div>
        </div>
    </div>
</div>
