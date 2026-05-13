<?php
$cancelHref = (string) ($cancelHref ?? url('/admin'));
$gpFormTitle = 'Mi perfil';
$gpFormBackUrl = $cancelHref;
$gpFormSubtitle = 'Datos de usuario del equipo (administración o monitor). La contraseña solo cambia si rellenas los campos.';
$gpFormBadge = 'Tu cuenta';
require dirname(__DIR__) . '/layouts/partials/gp_form_panel_start.php';
?>
                <p class="gp-form-required-legend text-muted small mb-3"><span class="text-danger fw-bold" aria-hidden="true">*</span> Obligatorio</p>
                <form action="<?= htmlspecialchars(url('/cuenta/perfil')) ?>" method="POST" class="needs-validation gp-form-stack" novalidate data-gp-validate="editClient" data-gp-unsaved-guard>
                    <input type="hidden" name="id" value="<?= isset($perfil['usuario_id']) ? (int) $perfil['usuario_id'] : '' ?>">
                    <div class="gp-form-grid gp-form-grid--2">
                        <div>
                            <label for="DNI_staff" class="form-label gp-label-required">DNI / NIE</label>
                            <input type="text" class="form-control font-monospace gp-doc-identidad-input" id="DNI_staff" name="DNI" value="<?= isset($perfil['DNI']) ? htmlspecialchars((string) $perfil['DNI']) : '' ?>" required maxlength="9" autocomplete="off" inputmode="text" placeholder="12345678A" data-gp-doc-identidad-es>
                            <p class="form-text small mb-0">8 números y letra, o NIE.</p>
                        </div>
                        <div>
                            <label for="telefono_staff" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono_staff" name="telefono" inputmode="numeric" autocomplete="tel" placeholder="612345678" pattern="[0-9+\s]{9,15}" value="<?= isset($perfil['telefono']) ? htmlspecialchars((string) $perfil['telefono']) : '' ?>">
                        </div>
                        <div class="gp-form-span-2">
                            <label for="nombre_staff" class="form-label gp-label-required">Nombre</label>
                            <input type="text" class="form-control" id="nombre_staff" name="nombre" value="<?= isset($perfil['nombre']) ? htmlspecialchars((string) $perfil['nombre']) : '' ?>" required>
                        </div>
                        <div>
                            <label for="apellido1_staff" class="form-label gp-label-required">Primer apellido</label>
                            <input type="text" class="form-control" id="apellido1_staff" name="apellido1" value="<?= isset($perfil['apellido1']) ? htmlspecialchars((string) $perfil['apellido1']) : '' ?>" required>
                        </div>
                        <div>
                            <label for="apellido2_staff" class="form-label">Segundo apellido</label>
                            <input type="text" class="form-control" id="apellido2_staff" name="apellido2" value="<?= isset($perfil['apellido2']) ? htmlspecialchars((string) $perfil['apellido2']) : '' ?>">
                        </div>
                        <div class="gp-form-span-2">
                            <label for="email_staff" class="form-label gp-label-required">Email</label>
                            <input type="email" class="form-control" id="email_staff" name="email" value="<?= isset($perfil['email']) ? htmlspecialchars((string) $perfil['email']) : '' ?>" required autocomplete="email">
                        </div>
                        <div>
                            <label for="clave_staff" class="form-label">Nueva contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="clave_staff" name="clave" autocomplete="new-password" minlength="16">
                                <button class="btn btn-outline-secondary" type="button" data-gp-pass-reveal-group="clave_staff,clave_confirmar_staff" aria-label="Mostrar u ocultar contraseñas">Ver</button>
                            </div>
                            <small class="text-muted">Déjalo vacío si no quieres cambiarla. Si la cambias: mín. 16 caracteres con mayúsculas, minúsculas, número y símbolo.</small>
                        </div>
                        <div>
                            <label for="clave_confirmar_staff" class="form-label">Confirmar nueva contraseña</label>
                            <input type="password" class="form-control" id="clave_confirmar_staff" name="clave_confirmar" autocomplete="new-password" minlength="16">
                        </div>
                    </div>
                    <div class="gp-form-actions">
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        <a href="<?= htmlspecialchars($cancelHref) ?>" class="btn btn-outline-secondary" data-gp-unsaved-cancel>Cancelar</a>
                    </div>
                </form>
<?php require dirname(__DIR__) . '/layouts/partials/gp_form_panel_end.php'; ?>
