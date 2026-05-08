<div class="content-wrapper">
    <div class="container-fluid mt-4">
        <?php
        $salas = $salas ?? [];
        $monitoresFiltro = $monitoresFiltro ?? [];
        ?>

        <div class="mb-3">
            <a href="<?= htmlspecialchars(url('/admin/actividades/crear')) ?>" class="btn btn-primary fw-semibold">+ Nueva actividad</a>
        </div>

        <h2 class="mb-3">Gestión de actividades</h2>

        <div
            class="gp-admin-grid gp-admin-card-panel"
            data-gp-admin-grid="actividades"
            data-endpoint="<?= htmlspecialchars(url('/admin/ajax/actividades')) ?>"
            data-colspan="10"
            data-url-edit-prefix="<?= htmlspecialchars(url('/admin/actividades/editar/')) ?>"
            data-url-del-prefix="<?= htmlspecialchars(url('/admin/actividades/eliminar/')) ?>">

            <form class="gp-admin-grid-filters row g-2 align-items-end mb-3" data-grid-filters novalidate>
                <div class="col-lg-5">
                    <label class="form-label small text-muted mb-0">Buscar global</label>
                    <input type="search" class="form-control form-control-sm" name="q" placeholder="Nombre, sala, monitor, descripción…" autocomplete="off">
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small text-muted mb-0">Nombre actividad</label>
                    <input type="text" class="form-control form-control-sm" name="nombre" autocomplete="off">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label small text-muted mb-0">Sala</label>
                    <select class="form-select form-select-sm" name="sala_id">
                        <option value="">Todas</option>
                        <?php foreach ($salas as $s): ?>
                            <option value="<?= (int) ($s['id'] ?? 0) ?>"><?= htmlspecialchars((string) ($s['nombre'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small text-muted mb-0">Monitor</label>
                    <select class="form-select form-select-sm" name="monitor_id">
                        <option value="">Todos</option>
                        <?php foreach ($monitoresFiltro as $m): ?>
                            <option value="<?= (int) ($m['monitor_id'] ?? 0) ?>">
                                <?= htmlspecialchars(trim(($m['nombre'] ?? '') . ' ' . ($m['apellido1'] ?? '') . ' ' . ($m['apellido2'] ?? ''))) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 col-lg-2">
                    <label class="form-label small text-muted mb-0">Día</label>
                    <select class="form-select form-select-sm" name="dia_semana">
                        <option value="">—</option>
                        <option value="L">Lunes</option>
                        <option value="M">Martes</option>
                        <option value="X">Miércoles</option>
                        <option value="J">Jueves</option>
                        <option value="V">Viernes</option>
                        <option value="S">Sábado</option>
                        <option value="D">Domingo</option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label small text-muted mb-0">Tipo</label>
                    <select class="form-select form-select-sm" name="recurrente">
                        <option value="">Todos</option>
                        <option value="1">Semanal (recurrente)</option>
                        <option value="0">Sesión puntual</option>
                    </select>
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
                <table class="table table-bordered align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Duración</th>
                            <th>Día</th>
                            <th>Hora inicio</th>
                            <th>Hora fin</th>
                            <th>Sala</th>
                            <th>Monitor</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody data-grid-body>
                        <tr><td colspan="10" class="text-muted py-4">Cargando…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
