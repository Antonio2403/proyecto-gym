<div class="content-wrapper">
    <div class="relative w-full px-3 mt-6">

        <header class="gp-page-header">
            <div class="gp-page-header__title">
                <h2 class="text-2xl mb-1">Gestión de monitores</h2>
                <p class="text-slate-500 text-sm">Alta, edición y baja del equipo de monitores.</p>
            </div>
            <div class="gp-view-toolbar">
                <a href="<?= htmlspecialchars(url('/admin/registrarMonitor')) ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1" aria-hidden="true"></i> Nuevo monitor
                </a>
            </div>
        </header>

        <div
            class="gp-admin-grid gp-admin-card-panel"
            data-gp-admin-grid="monitores"
            data-endpoint="<?= htmlspecialchars(url('/admin/ajax/monitores')) ?>"
            data-colspan="9"
            data-url-edit-prefix="<?= htmlspecialchars(url('/admin/monitores/editar/')) ?>"
            data-url-del-prefix="<?= htmlspecialchars(url('/admin/monitores/eliminar/')) ?>">

            <form class="gp-admin-grid-filters items-end mb-4 flex flex-wrap -mx-1 -mt-2" data-grid-filters novalidate>
                <div class="w-full xl:w-5/12 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Buscar global</label>
                    <input type="search" class="form-control form-control-sm" name="q" placeholder="Nombre, email, DNI, especialidad…" autocomplete="off">
                </div>
                <div class="w-full md:w-1/4 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">DNI</label>
                    <input type="text" class="form-control form-control-sm font-mono gp-doc-identidad-input" name="dni" maxlength="9" autocomplete="off" inputmode="text" placeholder="12345678A" data-gp-doc-identidad-es>
                </div>
                <div class="w-full md:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Email</label>
                    <input type="text" class="form-control form-control-sm" name="email" autocomplete="off">
                </div>
                <div class="w-full md:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Nombre / apellidos</label>
                    <input type="text" class="form-control form-control-sm" name="nombre" autocomplete="off">
                </div>
                <div class="w-full md:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Especialidad</label>
                    <input type="text" class="form-control form-control-sm" name="especialidad" autocomplete="off">
                </div>
                <div class="w-full md:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Disponibilidad</label>
                    <input type="text" class="form-control form-control-sm" name="disponibilidad" autocomplete="off">
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
                <div data-grid-pagination aria-label="Paginación"></div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>DNI</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Especialidad</th>
                            <th>Disponibilidad</th>
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
