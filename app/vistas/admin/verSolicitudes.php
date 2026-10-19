<div class="content-wrapper">
    <div class="relative w-full px-3 mt-6">
        <header class="gp-page-header">
            <div class="gp-page-header__title">
                <h2 class="text-2xl mb-1">Solicitudes pendientes</h2>
                <p class="text-slate-500 text-sm">Peticiones del equipo de monitores en espera de revisión.</p>
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

            <form class="gp-admin-grid-filters items-end mb-4 flex flex-wrap -mx-1 -mt-2" data-grid-filters novalidate>
                <div class="w-full lg:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Buscar</label>
                    <input type="search" name="q" class="form-control form-control-sm" placeholder="Tipo, texto, monitor, email…" autocomplete="off">
                </div>
                <div class="w-full md:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Tipo contiene</label>
                    <input type="text" name="tipo" class="form-control form-control-sm" autocomplete="off">
                </div>
                <div class="w-full md:w-2/3 lg:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Monitor (nombre/email)</label>
                    <input type="text" name="monitor" class="form-control form-control-sm" autocomplete="off">
                </div>
                <div class="w-full md:w-1/2 lg:w-1/6 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control form-control-sm">
                </div>
                <div class="w-full md:w-1/2 lg:w-1/6 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control form-control-sm">
                </div>
                <div class="flex flex-wrap gap-2 mt-2 w-full max-w-full shrink-0 px-1">
                    <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-grid-reset>Limpiar</button>
                    <div class="lg:ms-auto flex items-center gap-2">
                        <label class="text-sm text-slate-500 mb-0">Por página</label>
                        <select class="form-select form-select-sm" data-grid-per-page style="width: auto;">
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                        </select>
                    </div>
                </div>
            </form>

            <div class="flex justify-between items-center flex-wrap gap-2 mb-2">
                <small class="text-slate-500" data-grid-status>&nbsp;</small>
                <div data-grid-pagination></div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0 gp-data-table">
                    <thead class="text-sm">
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
                        <tr><td colspan="9" class="text-slate-500 py-6">Cargando…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
