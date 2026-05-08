<div class="content-wrapper">
    <div class="container-fluid mt-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h2 class="mb-1">Fisioterapeutas</h2>
                <p class="text-muted mb-0">Alta, edición y eliminación de profesionales disponibles para citas.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= htmlspecialchars(url('/admin/fisioterapeutas/nuevo')) ?>" class="btn btn-primary fw-semibold">
                    Nuevo fisio
                </a>
            </div>
        </div>

        <?php
        $notice = isset($_GET['success']) ? 'created' : (isset($_GET['updated']) ? 'updated' : (isset($_GET['deleted']) ? 'deleted' : null));
        if ($notice !== null): ?>
            <div class="alert alert-success gp-admin-alert" role="alert">
                <?php if ($notice === 'created'): ?>
                    Fisioterapeuta creado correctamente.
                <?php elseif ($notice === 'updated'): ?>
                    Cambios guardados correctamente.
                <?php else: ?>
                    Registro eliminado.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-danger gp-admin-alert" role="alert"><?= htmlspecialchars((string) $_GET['error']) ?></div>
        <?php endif; ?>

        <div
            class="gp-admin-grid gp-admin-card-panel p-0"
            data-gp-admin-grid="fisioterapeutas"
            data-endpoint="<?= htmlspecialchars(url('/admin/ajax/fisioterapeutas')) ?>"
            data-colspan="4"
            data-url-edit-prefix="<?= htmlspecialchars(url('/admin/fisioterapeutas/editar/')) ?>"
            data-url-del-prefix="<?= htmlspecialchars(url('/admin/fisioterapeutas/eliminar/')) ?>">

            <div class="p-3 border-bottom">
                <form class="gp-admin-grid-filters row g-2 align-items-end mb-0" data-grid-filters novalidate>
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label small text-muted mb-0">Buscar</label>
                        <input type="search" class="form-control form-control-sm" name="q" placeholder="Nombre o especialidad…" autocomplete="off">
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label small text-muted mb-0">Nombre</label>
                        <input type="text" class="form-control form-control-sm" name="nombre" autocomplete="off">
                    </div>
                    <div class="col-md-12 col-lg-4">
                        <label class="form-label small text-muted mb-0">Especialidad</label>
                        <input type="text" class="form-control form-control-sm" name="especialidad" autocomplete="off">
                    </div>
                    <div class="col-12 d-flex flex-wrap gap-2 mt-2 pt-2">
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
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 pt-2 mb-2">
                <small class="text-muted" data-grid-status>&nbsp;</small>
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
                        <tr><td colspan="4" class="text-muted py-4">Cargando…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
