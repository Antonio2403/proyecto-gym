<div class="content-wrapper">
    <div class="relative w-full px-3 mt-6">
        <header class="gp-page-header">
            <div class="gp-page-header__title">
                <h2 class="text-2xl mb-1">Fisioterapeutas</h2>
                <p class="text-slate-500 text-sm">Alta, edición y eliminación de profesionales disponibles para citas.</p>
            </div>
            <div class="gp-view-toolbar">
                <a href="<?= htmlspecialchars(url('/admin/fisioterapeutas/nuevo')) ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1" aria-hidden="true"></i> Nuevo fisio
                </a>
            </div>
        </header>

        <div
            class="gp-admin-grid gp-admin-card-panel p-0"
            data-gp-admin-grid="fisioterapeutas"
            data-endpoint="<?= htmlspecialchars(url('/admin/ajax/fisioterapeutas')) ?>"
            data-colspan="4"
            data-url-edit-prefix="<?= htmlspecialchars(url('/admin/fisioterapeutas/editar/')) ?>"
            data-url-del-prefix="<?= htmlspecialchars(url('/admin/fisioterapeutas/eliminar/')) ?>">

            <div class="p-4 border-b border-slate-300">
                <form class="gp-admin-grid-filters items-end mb-0 flex flex-wrap -mx-1 -mt-2" data-grid-filters novalidate>
                    <div class="w-full md:w-1/2 lg:w-1/3 max-w-full shrink-0 px-1 mt-2">
                        <label class="form-label text-sm text-slate-500 mb-0">Buscar</label>
                        <input type="search" class="form-control form-control-sm" name="q" placeholder="Nombre o especialidad…" autocomplete="off">
                    </div>
                    <div class="w-full md:w-1/2 lg:w-1/3 max-w-full shrink-0 px-1 mt-2">
                        <label class="form-label text-sm text-slate-500 mb-0">Nombre</label>
                        <input type="text" class="form-control form-control-sm" name="nombre" autocomplete="off">
                    </div>
                    <div class="w-full md:w-full lg:w-1/3 max-w-full shrink-0 px-1 mt-2">
                        <label class="form-label text-sm text-slate-500 mb-0">Especialidad</label>
                        <input type="text" class="form-control form-control-sm" name="especialidad" autocomplete="off">
                    </div>
                    <div class="flex flex-wrap gap-2 mt-2 pt-2 w-full max-w-full shrink-0 px-1">
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
            </div>

            <div class="flex justify-between items-center flex-wrap gap-2 px-4 pt-2 mb-2">
                <small class="text-slate-500" data-grid-status>&nbsp;</small>
                <div data-grid-pagination></div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Especialidad</th>
                            <th style="width: 200px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody data-grid-body>
                        <tr><td colspan="4" class="text-slate-500 py-6">Cargando…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
