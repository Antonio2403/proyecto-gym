<?php if (isset($_GET['error'])): ?>
    <?php $errorMsg = htmlspecialchars($_GET['error']); ?>
<?php endif; ?>

<div class="container mt-4">

    <div class="row justify-content-center">
        <div class="col-md-6">

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button">
                        Iniciar Sesión
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="registro-tab" data-bs-toggle="tab" data-bs-target="#registro" type="button">
                        Registrarse
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">

                <!-- LOGIN -->
                <div class="tab-pane fade show active" id="login">
                    <div class="card mt-3">
                        <div class="card-body">
                            <form action="/proyecto-gym/login" method="post">
                                <div class="mb-3">
                                    <label for="email_login" class="form-label">Email:</label>
                                    <input type="email" class="form-control" id="email_login" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="clave_login" class="form-label">Contraseña:</label>
                                    <input type="password" class="form-control" id="clave_login" name="clave" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- REGISTRO -->
                <div class="tab-pane fade" id="registro">
                    <div class="card mt-3">
                        <div class="card-body">
                            <form action="/proyecto-gym/usuario/registrar" method="POST">
                                <div class="mb-3">
                                    <label for="DNI" class="form-label">DNI:</label>
                                    <input type="text" class="form-control" id="DNI" name="DNI" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre:</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                                <div class="mb-3">
                                    <label for="apellido1" class="form-label">Primer Apellido:</label>
                                    <input type="text" class="form-control" id="apellido1" name="apellido1" required>
                                </div>
                                <div class="mb-3">
                                    <label for="apellido2" class="form-label">Segundo Apellido:</label>
                                    <input type="text" class="form-control" id="apellido2" name="apellido2">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email:</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="clave" class="form-label">Contraseña:</label>
                                    <input type="password" class="form-control" id="clave" name="clave" required>
                                </div>
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono:</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono">
                                </div>
                                <button type="submit" class="btn btn-primary">Registrarse</button>
                            </form>
                        </div>
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