<?php if (isset($_GET['error'])): ?>
    <?php $errorMsg = htmlspecialchars($_GET['error']); ?>
<?php endif; ?>

<div class="gp-auth-shell">
    <div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3">
        <div class="justify-center flex flex-wrap -mx-3">
            <div class="w-full lg:w-7/12 max-w-full shrink-0 px-3">
                <div class="text-center mb-6">
                    <span class="gp-badge">Tu cuenta</span>
                    <h1 class="text-[1.75rem] mt-4 mb-2">Editar datos del cliente</h1>
                    <p class="text-slate-500 text-sm mb-0">Actualiza tu información; la contraseña solo cambia si rellenas el campo.</p>
                </div>

                <div class="card border-0">
                    <div class="card-body p-6">
                        <p class="gp-form-required-legend text-slate-500 mb-4"><span class="text-red-600 font-bold" aria-hidden="true">*</span> Obligatorio</p>
                        <form action="<?= htmlspecialchars(url('/clientes/editar')) ?>" method="POST" class="needs-validation" novalidate data-gp-validate="editClient">
                            <input type="hidden" name="id" value="<?= isset($cliente['usuario_id']) ? htmlspecialchars($cliente['usuario_id']) : '' ?>">
                            <div class="flex flex-wrap -mx-2 -mt-4">
                                <div class="w-full md:w-1/2 max-w-full shrink-0 px-2 mt-4">
                                    <label for="DNI" class="form-label gp-label-required">DNI / NIE</label>
                                    <input type="text" class="form-control font-mono gp-doc-identidad-input" id="DNI" name="DNI" value="<?= isset($cliente['DNI']) ? htmlspecialchars($cliente['DNI']) : '' ?>" required maxlength="9" autocomplete="off" inputmode="text" placeholder="12345678A" data-gp-doc-identidad-es>
                                    <p class="form-text text-sm">8 números y letra, o NIE (X/Y/Z + 7 dígitos + letra).</p>
                                </div>
                                <div class="w-full md:w-1/2 max-w-full shrink-0 px-2 mt-4">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" inputmode="numeric" autocomplete="tel" placeholder="612345678" pattern="[0-9+\s]{9,15}" value="<?= isset($cliente['telefono']) ? htmlspecialchars($cliente['telefono']) : '' ?>">
                                </div>
                                <div class="w-full max-w-full shrink-0 px-2 mt-4">
                                    <label for="nombre" class="form-label gp-label-required">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= isset($cliente['nombre']) ? htmlspecialchars($cliente['nombre']) : '' ?>" required>
                                </div>
                                <div class="w-full md:w-1/2 max-w-full shrink-0 px-2 mt-4">
                                    <label for="apellido1" class="form-label gp-label-required">Primer apellido</label>
                                    <input type="text" class="form-control" id="apellido1" name="apellido1" value="<?= isset($cliente['apellido1']) ? htmlspecialchars($cliente['apellido1']) : '' ?>" required>
                                </div>
                                <div class="w-full md:w-1/2 max-w-full shrink-0 px-2 mt-4">
                                    <label for="apellido2" class="form-label">Segundo apellido</label>
                                    <input type="text" class="form-control" id="apellido2" name="apellido2" value="<?= isset($cliente['apellido2']) ? htmlspecialchars($cliente['apellido2']) : '' ?>">
                                </div>
                                <div class="w-full max-w-full shrink-0 px-2 mt-4">
                                    <label for="email" class="form-label gp-label-required">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= isset($cliente['email']) ? htmlspecialchars($cliente['email']) : '' ?>" required autocomplete="email">
                                </div>
                                <div class="w-full md:w-1/2 max-w-full shrink-0 px-2 mt-4">
                                    <label for="clave" class="form-label">Nueva contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="clave" name="clave" autocomplete="new-password" minlength="16">
                                        <button class="btn btn-outline-secondary" type="button" data-gp-pass-reveal-group="clave,clave_confirmar" aria-label="Mostrar u ocultar contraseñas">Ver</button>
                                    </div>
                                    <small class="text-slate-500">Déjalo vacío si no quieres cambiarla. Si la cambias: mín. 16 caracteres con mayúsculas, minúsculas, número y símbolo.</small>
                                </div>
                                <div class="w-full md:w-1/2 max-w-full shrink-0 px-2 mt-4">
                                    <label for="clave_confirmar" class="form-label">Confirmar nueva contraseña</label>
                                    <input type="password" class="form-control" id="clave_confirmar" name="clave_confirmar" autocomplete="new-password" minlength="16">
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 justify-end mt-6">
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
    <div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3 mt-4">
        <div class="alert alert-warning border-0 shadow-sm text-center"><?= $errorMsg ?></div>
    </div>
<?php endif; ?>
