<div class="content-wrapper py-4">
    <div class="relative w-full px-3 mt-6">
        <header class="gp-page-header">
            <div class="gp-page-header__title">
                <h3 class="text-2xl mb-1 text-slate-800">Mensajes de contacto</h3>
                <p class="text-slate-500 text-sm">Consulta y responde al feedback recibido desde la web.</p>
            </div>
        </header>

        <div
            class="gp-admin-grid gp-admin-card-panel border-0 shadow-sm"
            data-gp-admin-grid="feedback"
            data-endpoint="<?= htmlspecialchars(url('/admin/ajax/feedback')) ?>"
            data-colspan="7"
            data-url-del-prefix="<?= htmlspecialchars(url('/admin/feedback/eliminar/')) ?>"
            data-url-responder-prefix="<?= htmlspecialchars(url('/admin/feedback/responder/')) ?>">

            <form class="gp-admin-grid-filters items-end mb-4 flex flex-wrap -mx-1 -mt-2" data-grid-filters novalidate>
                <div class="w-full lg:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Buscar global</label>
                    <input type="search" class="form-control form-control-sm" name="q" autocomplete="off"
                           placeholder="Nombre, email, asunto, texto…">
                </div>
                <div class="w-full md:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Nombre</label>
                    <input type="text" class="form-control form-control-sm" name="nombre" autocomplete="off">
                </div>
                <div class="w-full md:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Email</label>
                    <input type="text" class="form-control form-control-sm" name="email" autocomplete="off">
                </div>
                <div class="w-full md:w-1/2 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Asunto contiene</label>
                    <input type="text" class="form-control form-control-sm" name="asunto" autocomplete="off">
                </div>
                <div class="w-full md:w-1/2 lg:w-1/4 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Desde fecha</label>
                    <input type="date" class="form-control form-control-sm" name="fecha_desde">
                </div>
                <div class="w-full md:w-1/2 lg:w-1/4 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Hasta fecha</label>
                    <input type="date" class="form-control form-control-sm" name="fecha_hasta">
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
                            <th>Fecha</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Asunto</th>
                            <th>Mensaje</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody data-grid-body>
                        <tr><td colspan="7" class="text-slate-500 py-6">Cargando…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
