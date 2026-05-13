<?php
$gpFormTitle = 'Editar sala';
$gpFormBackUrl = url('/monitor/verSalas');
$gpFormSubtitle = 'Actualiza los datos de la sala seleccionada.';
$gpFormBadge = 'Salas';
require dirname(__DIR__) . '/layouts/partials/gp_form_panel_start.php';
$dispVal = (string) ($sala['disponibilidad'] ?? '');
?>
                <p class="gp-form-required-legend text-muted small mb-3">Los campos con <span class="text-danger fw-bold" aria-hidden="true">*</span> son obligatorios (el ID es solo lectura).</p>
                <form action="<?= htmlspecialchars(url('/monitor/salas/editar/' . $sala['id'])) ?>" method="POST" class="needs-validation gp-form-stack" novalidate data-gp-validate="salaEdit"
                      data-gp-confirm data-gp-confirm-title="Guardar sala" data-gp-confirm-body="¿Guardar los cambios de la sala?" data-gp-confirm-ok="Guardar">
                    <div class="mb-3">
                        <label for="id" class="form-label">ID</label>
                        <input type="text" class="form-control" id="id" name="id" value="<?= htmlspecialchars((string) $sala['id']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label gp-label-required">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars((string) $sala['nombre']) ?>" required>
                    </div>
                    <div class="gp-form-grid gp-form-grid--2">
                        <div>
                            <label for="capacidad" class="form-label gp-label-required">Capacidad</label>
                            <input type="number" class="form-control" id="capacidad" name="capacidad" value="<?= htmlspecialchars((string) $sala['capacidad']) ?>" required min="1" max="10000" step="1">
                        </div>
                        <div>
                            <label for="disponibilidad" class="form-label gp-label-required">Disponibilidad</label>
                            <select class="form-select" id="disponibilidad" name="disponibilidad" required>
                                <option value="">Seleccionar...</option>
                                <option value="L"<?= $dispVal === 'L' ? ' selected' : '' ?>>Libre</option>
                                <option value="U"<?= $dispVal === 'U' ? ' selected' : '' ?>>En uso</option>
                                <option value="R"<?= $dispVal === 'R' ? ' selected' : '' ?>>Reservada</option>
                            </select>
                        </div>
                    </div>
                    <div class="gp-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1" aria-hidden="true"></i> Guardar cambios
                        </button>
                        <a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
<?php require dirname(__DIR__) . '/layouts/partials/gp_form_panel_end.php'; ?>
