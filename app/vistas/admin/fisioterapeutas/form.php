<?php
$fisio = $fisio ?? null;
$editando = is_array($fisio);
$gpFormTitle = $editando ? 'Editar fisioterapeuta' : 'Nuevo fisioterapeuta';
$gpFormBackUrl = url('/admin/fisioterapeutas');
$gpFormSubtitle = 'Alta o edición de ficha profesional. Máximo 100 caracteres por campo.';
$gpFormBadge = 'Fisioterapeutas';
require dirname(__DIR__, 2) . '/layouts/partials/gp_form_panel_start.php';
?>
                <p class="gp-form-required-legend text-muted small mb-3">Los campos con <span class="text-danger fw-bold" aria-hidden="true">*</span> son obligatorios.</p>
                <form
                    <?php if ($editando): ?>
                        action="<?= htmlspecialchars(url('/admin/fisioterapeutas/editar')) ?>"
                        method="post"
                    <?php else: ?>
                        action="<?= htmlspecialchars(url('/admin/fisioterapeutas/nuevo')) ?>"
                        method="post"
                    <?php endif; ?>
                    class="needs-validation gp-form-stack" novalidate data-gp-validate="adminFisio"
                    data-gp-confirm
                    data-gp-confirm-title="<?= htmlspecialchars($editando ? 'Guardar fisioterapeuta' : 'Crear fisioterapeuta') ?>"
                    data-gp-confirm-body="<?= htmlspecialchars($editando ? '¿Guardar los cambios?' : '¿Crear este fisioterapeuta?') ?>"
                    data-gp-confirm-ok="<?= htmlspecialchars($editando ? 'Guardar' : 'Crear') ?>">

                    <?php if ($editando): ?>
                        <input type="hidden" name="id" value="<?= (int) ($fisio['id'] ?? 0) ?>">
                    <?php endif; ?>

                    <div class="gp-form-grid gp-form-grid--2">
                        <div class="gp-form-span-2">
                            <label for="nombre" class="form-label gp-label-required">Nombre</label>
                            <input id="nombre" type="text" name="nombre" class="form-control" maxlength="100" required
                                   value="<?= $editando ? htmlspecialchars((string) ($fisio['nombre'] ?? '')) : '' ?>">
                            <div class="invalid-feedback">Indica un nombre (máx. 100 caracteres).</div>
                        </div>
                        <div class="gp-form-span-2">
                            <label for="especialidad" class="form-label">Especialidad</label>
                            <input id="especialidad" type="text" name="especialidad" class="form-control" maxlength="100"
                                   value="<?= $editando ? htmlspecialchars((string) ($fisio['especialidad'] ?? '')) : '' ?>">
                            <p class="form-text small mb-0">Ej. rehabilitación deportiva, fisiología, etc.</p>
                        </div>
                        <div class="gp-form-span-2">
                            <label for="usuario_email" class="form-label">Email de acceso al panel (opcional)</label>
                            <input id="usuario_email" type="email" name="usuario_email" class="form-control"
                                   maxlength="255" autocomplete="off"
                                   placeholder="ej. fisio@gym.com"
                                   value="<?= htmlspecialchars((string) ($usuario_email ?? '')) ?>">
                            <p class="form-text small mb-0">
                                Debe ser un usuario ya registrado (misma tabla de usuarios que el login principal), no socio, monitor ni admin.
                                Vacío = sin acceso al panel de fisioterapeuta o desvincular al editar.
                            </p>
                        </div>
                    </div>

                    <div class="gp-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1" aria-hidden="true"></i> <?= $editando ? 'Guardar cambios' : 'Crear fisioterapeuta' ?>
                        </button>
                        <a href="<?= htmlspecialchars(url('/admin/fisioterapeutas')) ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
<?php require dirname(__DIR__, 2) . '/layouts/partials/gp_form_panel_end.php'; ?>
