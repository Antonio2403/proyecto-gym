<div class="content-wrapper">
    <div class="container-fluid mt-4">
        <header class="gp-page-header">
            <div class="gp-page-header__title">
                <h2 class="h4 mb-1">Solicitudes pendientes</h2>
                <p class="text-muted small">Peticiones del equipo de monitores en espera de revisión.</p>
            </div>
            <div class="gp-view-toolbar">
                <a href="<?= htmlspecialchars(url('/admin/verSolicitudesAprobadas')) ?>" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-check me-1" aria-hidden="true"></i> Aprobadas
                </a>
                <a href="<?= htmlspecialchars(url('/admin/verSolicitudesRechazadas')) ?>" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-times me-1" aria-hidden="true"></i> Rechazadas
                </a>
                <a href="<?= htmlspecialchars(url('/admin')) ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Panel
                </a>
            </div>
        </header>

        <div
            class="gp-admin-grid gp-admin-card-panel"
            data-gp-admin-grid="solicitudes"
            data-admin-solicitudes-pendientes="1"
            data-solicitud-estado="P"
            data-endpoint="<?= htmlspecialchars(url('/admin/ajax/solicitudes')) ?>"
            data-colspan="9"
            data-aprobar-url="<?= htmlspecialchars(url('/admin/aprobar')) ?>">

            <form class="gp-admin-grid-filters row g-2 align-items-end mb-3" data-grid-filters novalidate>
                <div class="col-lg-4">
                    <label class="form-label small text-muted mb-0">Buscar</label>
                    <input type="search" name="q" class="form-control form-control-sm" placeholder="Tipo, texto, monitor, email…" autocomplete="off">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-0">Tipo contiene</label>
                    <input type="text" name="tipo" class="form-control form-control-sm" autocomplete="off">
                </div>
                <div class="col-md-8 col-lg-4">
                    <label class="form-label small text-muted mb-0">Monitor (nombre/email)</label>
                    <input type="text" name="monitor" class="form-control form-control-sm" autocomplete="off">
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label small text-muted mb-0">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control form-control-sm">
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label small text-muted mb-0">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control form-control-sm">
                </div>
                <div class="col-12 d-flex flex-wrap gap-2 mt-2">
                    <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-grid-reset>Limpiar</button>
                    <div class="ms-lg-auto d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0">Por página</label>
                        <select class="form-select form-select-sm" data-grid-per-page style="width: auto;">
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                        </select>
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <small class="text-muted" data-grid-status>&nbsp;</small>
                <div data-grid-pagination></div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0 gp-data-table">
                    <thead class="small">
                        <tr>
                            <th>ID</th>
                            <th>Monitor</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Motivo / descripción</th>
                            <th>Revisión</th>
                            <th>Estado</th>
                            <th class="gp-actions-col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody data-grid-body>
                        <tr><td colspan="9" class="text-muted py-4">Cargando…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
