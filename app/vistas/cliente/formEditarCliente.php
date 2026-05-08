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
                        <form action="<?= htmlspecialchars(url('/clientes/editar')) ?>" method="POST" class="needs-validation" novalidate data-gp-validate="editClient"
                              data-gp-confirm data-gp-confirm-title="Guardar perfil" data-gp-confirm-body="¿Guardar los cambios de tu perfil?" data-gp-confirm-ok="Guardar">
                            <input type="hidden" name="id" value="<?= isset($cliente['usuario_id']) ? htmlspecialchars($cliente['usuario_id']) : '' ?>">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="DNI" class="form-label gp-label-required">DNI / NIE</label>
                                    <input type="text" class="form-control" id="DNI" name="DNI" value="<?= isset($cliente['DNI']) ? htmlspecialchars($cliente['DNI']) : '' ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" value="<?= isset($cliente['telefono']) ? htmlspecialchars($cliente['telefono']) : '' ?>">
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
                                <div class="col-12">
                                    <label for="clave" class="form-label">Nueva contraseña</label>
                                    <input type="password" class="form-control" id="clave" name="clave" autocomplete="new-password" minlength="8">
                                    <small class="text-muted">Déjalo vacío si no quieres cambiarla.</small>
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

<!-- MODAL ERROR -->
<?php if (isset($errorMsg)): ?>
    <div class="modal fade" id="errorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                    <?= $errorMsg ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        });
    </script>
<?php endif; ?>
