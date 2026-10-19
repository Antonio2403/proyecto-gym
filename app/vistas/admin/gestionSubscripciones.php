<div class="content-wrapper">
    <div class="relative w-full px-3 mt-6">

        <header class="gp-page-header">
            <div class="gp-page-header__title">
                <h3 class="text-2xl mb-1">Suscripciones</h3>
                <p class="text-slate-500 text-sm">Planes de membresía del centro.</p>
            </div>
            <div class="gp-view-toolbar">
                <a href="<?= htmlspecialchars(url('/inicioAdmin')) ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Volver
                </a>
                <a href="<?= htmlspecialchars(url('/admin/formSubscripcion')) ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1" aria-hidden="true"></i> Crear suscripción
                </a>
            </div>
        </header>

        <div
            class="gp-admin-grid gp-admin-card-panel"
            data-gp-admin-grid="subscripciones"
            data-endpoint="<?= htmlspecialchars(url('/admin/ajax/subscripciones')) ?>"
            data-colspan="9"
            data-url-edit-base="<?= htmlspecialchars(url('/admin/formEditarSubscripcion')) ?>"
            data-url-delete="<?= htmlspecialchars(url('/admin/eliminarSubscripcion')) ?>">

            <form class="gp-admin-grid-filters items-end mb-4 flex flex-wrap -mx-1 -mt-2" data-grid-filters novalidate>
                <div class="w-full lg:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Buscar (nombre)</label>
                    <input type="search" class="form-control form-control-sm" name="q" placeholder="Nombre del plan…" autocomplete="off">
                </div>
                <div class="w-full md:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Nombre contiene</label>
                    <input type="text" class="form-control form-control-sm" name="nombre" autocomplete="off">
                </div>
                <div class="w-full md:w-1/3 lg:w-1/6 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Precio desde (€)</label>
                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="precio_min" autocomplete="off">
                </div>
                <div class="w-full md:w-1/3 lg:w-1/6 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Precio hasta (€)</label>
                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="precio_max" autocomplete="off">
                </div>
                <div class="w-full md:w-1/2 lg:w-1/6 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Duración mín (meses)</label>
                    <input type="number" min="1" class="form-control form-control-sm" name="duracion_min" autocomplete="off">
                </div>
                <div class="w-full md:w-1/2 lg:w-1/6 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Duración máx (meses)</label>
                    <input type="number" min="1" class="form-control form-control-sm" name="duracion_max" autocomplete="off">
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
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Duración</th>
                            <th>Clases/semana</th>
                            <th>Fisio</th>
                            <th>Oferta</th>
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
