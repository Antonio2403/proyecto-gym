<?php
$gpFormTitle = 'Editar material';
$gpFormBackUrl = url('/monitor/salas/' . (int) $sala_id . '/materiales');
$gpFormSubtitle = 'Actualiza el nombre y el estado del material seleccionado.';
$gpFormBadge = 'Materiales';
require dirname(__DIR__) . '/layouts/partials/gp_form_panel_start.php';
?>
                <p class="gp-form-required-legend text-muted small mb-3">Nombre y estado son obligatorios (<span class="text-danger fw-bold" aria-hidden="true">*</span>).</p>
                <form method="POST" action="<?= htmlspecialchars(url('/monitor/salas/' . (int) $sala_id . '/materiales/editar/' . (int) $material['id'])) ?>" class="needs-validation gp-form-stack" novalidate data-gp-validate="materialEdit"
                      data-gp-confirm data-gp-confirm-title="Guardar material" data-gp-confirm-body="¿Guardar los cambios del material?" data-gp-confirm-ok="Guardar">
                    <div class="gp-form-grid gp-form-grid--2">
                        <div>
                            <label for="id" class="form-label">ID</label>
                            <input type="text" class="form-control" id="id" name="id" value="<?php echo htmlspecialchars($material['id']); ?>" readonly>
                        </div>
                        <div>
                            <label for="estado_sel" class="form-label gp-label-required">Estado</label>
                            <select class="form-select" id="estado_sel" name="estado" required>
                                <option value="B" <?= (($material['estado'] ?? '') === 'B') ? ' selected' : '' ?>>Buen estado</option>
                                <option value="M" <?= (($material['estado'] ?? '') === 'M') ? ' selected' : '' ?>>Mal estado</option>
                            </select>
                        </div>
                        <div class="gp-form-span-2">
                            <label for="nombre" class="form-label gp-label-required">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($material['nombre']); ?>" required maxlength="200">
                        </div>
                    </div>
                    <div class="gp-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1" aria-hidden="true"></i> Guardar cambios
                        </button>
                        <a href="<?= htmlspecialchars(url('/monitor/salas/' . (int) $sala_id . '/materiales')) ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
<?php require dirname(__DIR__) . '/layouts/partials/gp_form_panel_end.php'; ?>
