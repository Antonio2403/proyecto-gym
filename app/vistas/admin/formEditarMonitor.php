<?php
$gpFormTitle = 'Editar monitor';
$gpFormBackUrl = url('/admin/verMonitores');
$gpFormSubtitle = 'Actualiza los datos personales y profesionales del monitor.';
$gpFormBadge = 'Monitores';
require dirname(__DIR__) . '/layouts/partials/gp_form_panel_start.php';
?>
                <p class="gp-form-required-legend text-muted small mb-3">Los campos con <span class="text-danger fw-bold" aria-hidden="true">*</span> son obligatorios.</p>
                <form action="<?= htmlspecialchars(url('/admin/monitores/editar')) ?>" method="POST" class="needs-validation gp-form-stack" novalidate data-gp-validate="monitorEdit"
                      data-gp-confirm data-gp-confirm-title="Guardar monitor" data-gp-confirm-body="¿Guardar los cambios de este monitor?" data-gp-confirm-ok="Guardar">

                    <input type="hidden" name="id" value="<?= (int) $monitor['monitor_id'] ?>">

                    <div class="gp-form-grid gp-form-grid--2">
                        <div>
                            <label for="DNI" class="form-label gp-label-required">DNI / NIE</label>
                            <input type="text" class="form-control font-monospace gp-doc-identidad-input" id="DNI" name="DNI"
                                value="<?= htmlspecialchars((string) $monitor['DNI'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="9" autocomplete="off" inputmode="text" placeholder="12345678A" data-gp-doc-identidad-es>
                            <p class="form-text small mb-0">8 números y letra, o NIE.</p>
                        </div>
                        <div>
                            <label for="telefono" class="form-label gp-label-required">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono"
                                value="<?= htmlspecialchars((string) $monitor['telefono'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="gp-form-span-2">
                            <label for="nombre" class="form-label gp-label-required">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                value="<?= htmlspecialchars((string) $monitor['nombre'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div>
                            <label for="apellido1" class="form-label gp-label-required">Primer apellido</label>
                            <input type="text" class="form-control" id="apellido1" name="apellido1"
                                value="<?= htmlspecialchars((string) $monitor['apellido1'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div>
                            <label for="apellido2" class="form-label">Segundo apellido</label>
                            <input type="text" class="form-control" id="apellido2" name="apellido2"
                                value="<?= htmlspecialchars((string) ($monitor['apellido2'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="gp-form-span-2">
                            <label for="email" class="form-label gp-label-required">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= htmlspecialchars((string) $monitor['email'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div>
                            <label for="clave_mon" class="form-label">Nueva contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="clave_mon" name="clave" minlength="16" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" data-gp-pass-reveal-group="clave_mon,clave_mon_c" aria-label="Mostrar u ocultar contraseñas">Ver</button>
                            </div>
                            <small class="text-muted">Déjalo vacío si no quieres cambiarla. Si la cambias: mín. 16 caracteres con mayúsculas, minúsculas, número y símbolo.</small>
                        </div>
                        <div>
                            <label for="clave_mon_c" class="form-label">Confirmar nueva contraseña</label>
                            <input type="password" class="form-control" id="clave_mon_c" name="clave_confirmar" minlength="16" autocomplete="new-password">
                        </div>
                        <div>
                            <label for="especialidad" class="form-label gp-label-required">Especialidad</label>
                            <input type="text" class="form-control" id="especialidad" name="especialidad"
                                value="<?= htmlspecialchars((string) $monitor['especialidad'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div>
                            <label for="disponibilidad" class="form-label gp-label-required">Disponibilidad</label>
                            <input type="text" class="form-control" id="disponibilidad" name="disponibilidad"
                                value="<?= htmlspecialchars((string) $monitor['disponibilidad'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Ej: mañanas, tardes..." required>
                        </div>
                    </div>

                    <div class="gp-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1" aria-hidden="true"></i> Actualizar monitor
                        </button>
                        <a href="<?= htmlspecialchars(url('/admin/verMonitores')) ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
<?php require dirname(__DIR__) . '/layouts/partials/gp_form_panel_end.php'; ?>
