<div class="content-wrapper">
    <div class="relative w-full px-3 mt-6">

        <header class="gp-page-header">
            <div class="gp-page-header__title">
                <h2 class="text-2xl mb-1">Solicitudes rechazadas</h2>
            </div>
            <div class="gp-view-toolbar">
                <a href="<?= htmlspecialchars(url('/admin/verSolicitudes')) ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Pendientes
                </a>
            </div>
        </header>

        <div
            class="gp-admin-grid gp-admin-card-panel"
            data-gp-admin-grid="solicitudes"
            data-solicitud-estado="R"
            data-endpoint="<?= htmlspecialchars(url('/admin/ajax/solicitudes')) ?>"
            data-colspan="9"
            data-aprobar-url="<?= htmlspecialchars(url('/admin/aprobar')) ?>">

            <form class="gp-admin-grid-filters items-end mb-4 flex flex-wrap -mx-1 -mt-2" data-grid-filters novalidate>
                <div class="w-full lg:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Buscar</label>
                    <input type="search" name="q" class="form-control form-control-sm" autocomplete="off">
                </div>
                <div class="w-full md:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Tipo</label>
                    <input type="text" name="tipo" class="form-control form-control-sm" autocomplete="off">
                </div>
                <div class="w-full md:w-2/3 lg:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Monitor</label>
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
                <table class="table table-bordered align-middle mb-0">
                    <thead class="text-sm">
                        <tr>
                            <th>ID</th>
                            <th>Monitor</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Fecha alta</th>
                            <th>Motivo</th>
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
