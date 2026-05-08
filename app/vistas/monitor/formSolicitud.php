<a href="<?= htmlspecialchars(url('/monitor/verMonitorSolicitudes')) ?>" class="btn btn-secondary btn-sm mb-3">Volver</a>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Crear Solicitud</h5>

                    <?php if (!empty($_GET['error'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars((string) $_GET['error']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($_GET['success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars((string) $_GET['success']) ?></div>
                    <?php endif; ?>

                    <p class="gp-form-required-legend text-muted small mb-3">El tipo es obligatorio (<span class="text-danger fw-bold" aria-hidden="true">*</span>).</p>

                    <form action="<?= htmlspecialchars(url('/monitor/crearSolicitud')) ?>" method="POST" class="needs-validation" novalidate data-gp-validate="monitorSolicitud"
                          data-gp-confirm data-gp-confirm-title="Enviar solicitud" data-gp-confirm-body="¿Enviar esta solicitud al centro para su revisión?" data-gp-confirm-ok="Enviar">
                        <div class="mb-3">
                            <label for="tipo" class="form-label gp-label-required">Tipo de solicitud</label>
                            <input type="text" class="form-control" id="tipo" name="tipo" required maxlength="120">
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción (opcional)</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" maxlength="4000"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Enviar solicitud</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
