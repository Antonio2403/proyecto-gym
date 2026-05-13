<?php if (isset($_GET['error'])): ?>
    <?php $errorMsg = htmlspecialchars($_GET['error']); ?>
<?php endif; ?>

<div class="gp-auth-shell">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="text-center mb-4">
                    <span class="gp-badge">Tu cuenta</span>
                    <h1 class="h3 mt-3 mb-2">Editar datos del cliente</h1>
                    <p class="text-muted small mb-0">Actualiza tu información; la contraseña solo cambia si rellenas el campo.</p>
                </div>

                <div class="card border-0">
                    <div class="card-body p-4">
                        <p class="gp-form-required-legend text-muted mb-3"><span class="text-danger fw-bold" aria-hidden="true">*</span> Obligatorio</p>
                        <form action="<?= htmlspecialchars(url('/clientes/editar')) ?>" method="POST" class="needs-validation" novalidate data-gp-validate="editClient">
                            <input type="hidden" name="id" value="<?= isset($cliente['usuario_id']) ? htmlspecialchars($cliente['usuario_id']) : '' ?>">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="DNI" class="form-label gp-label-required">DNI / NIE</label>
                                    <input type="text" class="form-control font-monospace gp-doc-identidad-input" id="DNI" name="DNI" value="<?= isset($cliente['DNI']) ? htmlspecialchars($cliente['DNI']) : '' ?>" required maxlength="9" autocomplete="off" inputmode="text" placeholder="12345678A" data-gp-doc-identidad-es>
                                    <p class="form-text small">8 números y letra, o NIE (X/Y/Z + 7 dígitos + letra).</p>
                                </div>
                                <div class="col-md-6">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" inputmode="numeric" autocomplete="tel" placeholder="612345678" pattern="[0-9+\s]{9,15}" value="<?= isset($cliente['telefono']) ? htmlspecialchars($cliente['telefono']) : '' ?>">
                                </div>
                                <div class="col-12">
                                    <label for="nombre" class="form-label gp-label-required">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= isset($cliente['nombre']) ? htmlspecialchars($cliente['nombre']) : '' ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="apellido1" class="form-label gp-label-required">Primer apellido</label>
                                    <input type="text" class="form-control" id="apellido1" name="apellido1" value="<?= isset($cliente['apellido1']) ? htmlspecialchars($cliente['apellido1']) : '' ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="apellido2" class="form-label">Segundo apellido</label>
                                    <input type="text" class="form-control" id="apellido2" name="apellido2" value="<?= isset($cliente['apellido2']) ? htmlspecialchars($cliente['apellido2']) : '' ?>">
                                </div>
                                <div class="col-12">
                                    <label for="email" class="form-label gp-label-required">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= isset($cliente['email']) ? htmlspecialchars($cliente['email']) : '' ?>" required autocomplete="email">
                                </div>
                                <div class="col-md-6">
                                    <label for="clave" class="form-label">Nueva contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="clave" name="clave" autocomplete="new-password" minlength="16">
                                        <button class="btn btn-outline-secondary" type="button" data-gp-pass-reveal-group="clave,clave_confirmar" aria-label="Mostrar u ocultar contraseñas">Ver</button>
                                    </div>
                                    <small class="text-muted">Déjalo vacío si no quieres cambiarla. Si la cambias: mín. 16 caracteres con mayúsculas, minúsculas, número y símbolo.</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="clave_confirmar" class="form-label">Confirmar nueva contraseña</label>
                                    <input type="password" class="form-control" id="clave_confirmar" name="clave_confirmar" autocomplete="new-password" minlength="16">
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">
                                <a href="<?= htmlspecialchars(url('/inicioUsuario')) ?>" class="btn btn-outline-light">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($errorMsg)): ?>
    <div class="container mt-3">
        <div class="alert alert-warning border-0 shadow-sm text-center"><?= $errorMsg ?></div>
    </div>
<?php endif; ?>
