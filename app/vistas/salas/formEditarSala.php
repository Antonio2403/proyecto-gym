<div class="content-wrapper">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="mb-4">Editar Sala</h1>
            <p class="gp-form-required-legend text-muted small mb-3">Los campos con <span class="text-danger fw-bold" aria-hidden="true">*</span> son obligatorios (el ID es solo lectura).</p>
            <form action="<?= htmlspecialchars(url('/monitor/salas/editar/' . $sala['id'])) ?>" method="POST" class="needs-validation" novalidate data-gp-validate="salaEdit"
                  data-gp-confirm data-gp-confirm-title="Guardar sala" data-gp-confirm-body="¿Guardar los cambios de la sala?" data-gp-confirm-ok="Guardar">
                <div class="mb-3">
                    <label for="id" class="form-label">ID</label>
                    <input type="text" class="form-control" id="id" name="id" value="<?php echo htmlspecialchars($sala['id']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label gp-label-required">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($sala['nombre']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="capacidad" class="form-label gp-label-required">Capacidad</label>
                    <input type="number" class="form-control" id="capacidad" name="capacidad" value="<?php echo htmlspecialchars($sala['capacidad']); ?>" required min="1" max="10000" step="1">
                </div>
                <div class="mb-3">
                    <label for="disponibilidad" class="form-label gp-label-required">Disponibilidad</label>
                    <input type="text" class="form-control" id="disponibilidad" name="disponibilidad" value="<?php echo htmlspecialchars($sala['disponibilidad']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Guardar Cambios</button>
                <a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>" class="btn btn-secondary w-100 mt-2">Cancelar</a>
            </form>
        </div>
    </div>
</div>