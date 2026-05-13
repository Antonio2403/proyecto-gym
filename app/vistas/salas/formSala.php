<?php
$gpFormTitle = 'Crear sala';
$gpFormBackUrl = url('/monitor/verSalas');
$gpFormSubtitle = 'Añade una sala al inventario del centro.';
$gpFormBadge = 'Salas';
require dirname(__DIR__) . '/layouts/partials/gp_form_panel_start.php';
?>
                <p class="gp-form-required-legend text-muted small mb-3">Los campos con <span class="text-danger fw-bold" aria-hidden="true">*</span> son obligatorios.</p>
                <form action="<?= htmlspecialchars(url('/monitor/salas/crear')) ?>" method="POST" class="needs-validation gp-form-stack" novalidate data-gp-validate="salaCreate"
                      data-gp-confirm data-gp-confirm-title="Crear sala" data-gp-confirm-body="¿Crear esta sala en el centro?" data-gp-confirm-ok="Crear">
                    <div class="mb-3">
                        <label for="nombre" class="form-label gp-label-required">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="gp-form-grid gp-form-grid--2">
                        <div>
                            <label for="capacidad" class="form-label gp-label-required">Capacidad</label>
                            <input type="number" class="form-control" id="capacidad" name="capacidad" required min="1" max="10000" step="1">
                        </div>
                        <div>
                            <label for="disponibilidad" class="form-label gp-label-required">Disponibilidad</label>
                            <select class="form-select" id="disponibilidad" name="disponibilidad" required>
                                <option value="">Seleccionar...</option>
                                <option value="L">Libre</option>
                                <option value="U">En uso</option>
                                <option value="R">Reservada</option>
                            </select>
                        </div>
                    </div>
                    <div class="gp-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-1" aria-hidden="true"></i> Crear sala
                        </button>
                        <a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
<?php require dirname(__DIR__) . '/layouts/partials/gp_form_panel_end.php'; ?>
