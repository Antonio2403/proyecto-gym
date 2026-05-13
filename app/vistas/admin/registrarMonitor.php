<?php
$gpFormTitle = 'Crear monitor';
$gpFormBackUrl = url('/admin/verMonitores');
$gpFormSubtitle = 'Alta de cuenta de monitor con datos personales y profesionales.';
$gpFormBadge = 'Monitores';
require dirname(__DIR__) . '/layouts/partials/gp_form_panel_start.php';
?>
                <p class="gp-form-required-legend text-muted small mb-3">Los campos con <span class="text-danger fw-bold" aria-hidden="true">*</span> son obligatorios.</p>
                <form action="<?= htmlspecialchars(url('/admin/crearMonitor')) ?>" method="POST" class="needs-validation gp-form-stack" novalidate data-gp-validate="monitorCreate"
                      data-gp-confirm data-gp-confirm-title="Crear monitor" data-gp-confirm-body="¿Crear esta cuenta de monitor?" data-gp-confirm-ok="Crear">

                    <div class="gp-form-grid gp-form-grid--2">
                        <div>
                            <label for="DNI" class="form-label gp-label-required">DNI / NIE</label>
                            <input type="text" class="form-control font-monospace gp-doc-identidad-input" id="DNI" name="DNI" required maxlength="9" autocomplete="off" inputmode="text" placeholder="12345678A" data-gp-doc-identidad-es>
                            <p class="form-text small mb-0">8 números y letra, o NIE.</p>
                        </div>
                        <div>
                            <label for="telefono" class="form-label gp-label-required">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required>
                        </div>
                        <div class="gp-form-span-2">
                            <label for="nombre" class="form-label gp-label-required">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div>
                            <label for="apellido1" class="form-label gp-label-required">Primer apellido</label>
                            <input type="text" class="form-control" id="apellido1" name="apellido1" required>
                        </div>
                        <div>
                            <label for="apellido2" class="form-label">Segundo apellido</label>
                            <input type="text" class="form-control" id="apellido2" name="apellido2">
                        </div>
                        <div class="gp-form-span-2">
                            <label for="email" class="form-label gp-label-required">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div>
                            <label for="clave" class="form-label gp-label-required">Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="clave" name="clave" required minlength="16" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" data-gp-pass-reveal-group="clave,clave_confirmar" aria-label="Mostrar u ocultar contraseñas">Ver</button>
                            </div>
                            <small class="text-muted">Mín. 16 caracteres con mayúsculas, minúsculas, número y símbolo.</small>
                        </div>
                        <div>
                            <label for="clave_confirmar" class="form-label gp-label-required">Confirmar contraseña</label>
                            <input type="password" class="form-control" id="clave_confirmar" name="clave_confirmar" required minlength="16" autocomplete="new-password">
                        </div>
                        <div>
                            <label for="especialidad" class="form-label gp-label-required">Especialidad</label>
                            <input type="text" class="form-control" id="especialidad" name="especialidad" required>
                        </div>
                        <div>
                            <label for="disponibilidad" class="form-label gp-label-required">Disponibilidad</label>
                            <input type="text" class="form-control" id="disponibilidad" name="disponibilidad" placeholder="Ej: mañanas, tardes..." required>
                        </div>
                    </div>

                    <div class="gp-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-1" aria-hidden="true"></i> Crear monitor
                        </button>
                        <a href="<?= htmlspecialchars(url('/admin/verMonitores')) ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
<?php require dirname(__DIR__) . '/layouts/partials/gp_form_panel_end.php'; ?>
