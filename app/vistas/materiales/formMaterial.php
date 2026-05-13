<?php
$gpFormTitle = 'Registrar material';
$gpFormBackUrl = url('/monitor/salas/' . (int) $sala_id . '/materiales');
$gpFormSubtitle = 'Añade un elemento al inventario de esta sala.';
$gpFormBadge = 'Materiales';
require dirname(__DIR__) . '/layouts/partials/gp_form_panel_start.php';
?>
                <p class="gp-form-required-legend text-muted small mb-3">Los campos con <span class="text-danger fw-bold" aria-hidden="true">*</span> son obligatorios.</p>
                <form method="POST" action="<?= htmlspecialchars(url('/monitor/salas/' . $sala_id . '/materiales/crear')) ?>" class="needs-validation gp-form-stack" novalidate data-gp-validate="materialCreate"
                      data-gp-confirm data-gp-confirm-title="Registrar material" data-gp-confirm-body="¿Dar de alta este material en la sala?" data-gp-confirm-ok="Registrar">
                    <div class="gp-form-grid gp-form-grid--2">
                        <div>
                            <label for="nombre" class="form-label gp-label-required">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="200">
                        </div>
                        <div>
                            <label for="estado" class="form-label gp-label-required">Estado</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="">Seleccionar estado</option>
                                <option value="B">Buen estado</option>
                                <option value="M">Mal estado</option>
                            </select>
                        </div>
                    </div>
                    <div class="gp-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-1" aria-hidden="true"></i> Registrar material
                        </button>
                        <a href="<?= htmlspecialchars(url('/monitor/salas/' . (int) $sala_id . '/materiales')) ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
<?php require dirname(__DIR__) . '/layouts/partials/gp_form_panel_end.php'; ?>
