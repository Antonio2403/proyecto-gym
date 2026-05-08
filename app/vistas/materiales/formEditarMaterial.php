<div class="content-wrapper">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="mb-4">Editar material</h1>
            <p class="gp-form-required-legend text-muted small mb-3">Nombre y estado son obligatorios (<span class="text-danger fw-bold" aria-hidden="true">*</span>).</p>
            <form method="POST" action="<?= htmlspecialchars(url('/monitor/salas/' . (int) $sala_id . '/materiales/editar/' . (int) $material['id'])) ?>" class="needs-validation" novalidate data-gp-validate="materialEdit"
                  data-gp-confirm data-gp-confirm-title="Guardar material" data-gp-confirm-body="¿Guardar los cambios del material?" data-gp-confirm-ok="Guardar">
                <div class="mb-3">
                    <label for="id" class="form-label">ID</label>
                    <input type="text" class="form-control" id="id" name="id" value="<?php echo htmlspecialchars($material['id']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label gp-label-required">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($material['nombre']); ?>" required maxlength="200">
                </div>
                <div class="mb-3">
                    <label for="estado_sel" class="form-label gp-label-required">Estado</label>
                    <select class="form-control" id="estado_sel" name="estado" required>
                        <option value="B" <?= (($material['estado'] ?? '') === 'B') ? ' selected' : '' ?>>Buen estado</option>
                        <option value="M" <?= (($material['estado'] ?? '') === 'M') ? ' selected' : '' ?>>Mal estado</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Guardar Cambios</button>
                <a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>" class="btn btn-secondary w-100 mt-2">Cancelar</a>
            </form>
        </div>
    </div>
</div>