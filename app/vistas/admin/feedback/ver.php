<div class="content-wrapper py-3">
    <div class="container-fluid mt-4">
        <header class="gp-page-header">
            <div class="gp-page-header__title">
                <h3 class="h4 mb-1 text-dark">Mensajes de contacto</h3>
                <p class="text-muted small">Consulta y responde al feedback recibido desde la web.</p>
            </div>
        </header>

        <div
            class="gp-admin-grid gp-admin-card-panel border-0 shadow-sm"
            data-gp-admin-grid="feedback"
            data-endpoint="<?= htmlspecialchars(url('/admin/ajax/feedback')) ?>"
            data-colspan="7"
            data-url-del-prefix="<?= htmlspecialchars(url('/admin/feedback/eliminar/')) ?>"
            data-url-responder-prefix="<?= htmlspecialchars(url('/admin/feedback/responder/')) ?>">

            <form class="gp-admin-grid-filters row g-2 align-items-end mb-3" data-grid-filters novalidate>
                <div class="col-lg-4">
                    <label class="form-label small text-muted mb-0">Buscar global</label>
                    <input type="search" class="form-control form-control-sm" name="q" autocomplete="off"
                           placeholder="Nombre, email, asunto, texto…">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-0">Nombre</label>
                    <input type="text" class="form-control form-control-sm" name="nombre" autocomplete="off">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-0">Email</label>
                    <input type="text" class="form-control form-control-sm" name="email" autocomplete="off">
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted mb-0">Asunto contiene</label>
                    <input type="text" class="form-control form-control-sm" name="asunto" autocomplete="off">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label small text-muted mb-0">Desde fecha</label>
                    <input type="date" class="form-control form-control-sm" name="fecha_desde">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label small text-muted mb-0">Hasta fecha</label>
                    <input type="date" class="form-control form-control-sm" name="fecha_hasta">
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
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Asunto</th>
                            <th>Mensaje</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody data-grid-body>
                        <tr><td colspan="7" class="text-muted py-4">Cargando…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
