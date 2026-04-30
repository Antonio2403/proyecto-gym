<?php if (isset($_GET['error'])): ?>
    <?php $errorMsg = htmlspecialchars($_GET['error']); ?>
<?php endif; ?>

<div class="container mt-4">

    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Editar Datos del Cliente</h5>
                </div>
                <div class="card-body">
                    <form action="/proyecto-gym/clientes/editar" method="POST">
                        <input type="hidden" name="id" value="<?= isset($cliente['usuario_id']) ? htmlspecialchars($cliente['usuario_id']) : '' ?>">
                        <div class="mb-3">
                            <label for="DNI" class="form-label">DNI:</label>
                            <input type="text" class="form-control" id="DNI" name="DNI" value="<?= isset($cliente['DNI']) ? htmlspecialchars($cliente['DNI']) : '' ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= isset($cliente['nombre']) ? htmlspecialchars($cliente['nombre']) : '' ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido1" class="form-label">Primer Apellido:</label>
                            <input type="text" class="form-control" id="apellido1" name="apellido1" value="<?= isset($cliente['apellido1']) ? htmlspecialchars($cliente['apellido1']) : '' ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido2" class="form-label">Segundo Apellido:</label>
                            <input type="text" class="form-control" id="apellido2" name="apellido2" value="<?= isset($cliente['apellido2']) ? htmlspecialchars($cliente['apellido2']) : '' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= isset($cliente['email']) ? htmlspecialchars($cliente['email']) : '' ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="clave" class="form-label">Contraseña:</label>
                            <input type="password" class="form-control" id="clave" name="clave">
                            <small class="text-muted">Déjalo vacío si no quieres cambiarla</small>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono:</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" value="<?= isset($cliente['telefono']) ? htmlspecialchars($cliente['telefono']) : '' ?>">
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            <a href="/proyecto-gym/admin/verClientes" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
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