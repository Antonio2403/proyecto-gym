<?php if (isset($_GET['error'])): ?>
    <?php $errorMsg = htmlspecialchars($_GET['error']); ?>
<?php endif; ?>
<?php if (isset($_GET['success'])): ?>
    <?php $successMsg = htmlspecialchars($_GET['success']); ?>
<?php endif; ?>

<div class="gp-auth-shell">
    <div class="container">
        <div class="gp-auth-card">
            <div class="text-center mb-4">
                <span class="gp-badge">Acceso miembros</span>
                <h1 class="h3 mt-3 mb-2">Entra a Spartum</h1>
                <p class="text-muted small mb-0">Sesión segura · Registro en un solo lugar</p>
            </div>

            <ul class="nav nav-tabs nav-fill mb-0" id="authTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active w-100" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">
                        Iniciar sesión
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link w-100" id="registro-tab" data-bs-toggle="tab" data-bs-target="#registro" type="button" role="tab">
                        Registrarse
                    </button>
                </li>
            </ul>

            <div class="card border-top-0 rounded-top-0">
                <div class="card-body p-4">
                    <p class="gp-form-required-legend text-muted text-center mb-3">Campos obligatorios: <span class="text-danger fw-bold" aria-hidden="true">*</span></p>
                    <div class="tab-content" id="authTabsContent">

                        <div class="tab-pane fade show active" id="login" role="tabpanel">
                            <form action="<?= htmlspecialchars(url('/login')) ?>" method="post" class="needs-validation" novalidate data-gp-validate="login"
                                  data-gp-confirm data-gp-confirm-title="Iniciar sesión" data-gp-confirm-body="¿Continuar para entrar en tu cuenta?" data-gp-confirm-ok="Entrar">
                                <div class="mb-3">
                                    <label for="email_login" class="form-label gp-label-required">Email</label>
                                    <input type="email" class="form-control form-control-lg" id="email_login" name="email" required autocomplete="email">
                                </div>
                                <div class="mb-4">
                                    <label for="clave_login" class="form-label gp-label-required">Contraseña</label>
                                    <input type="password" class="form-control form-control-lg" id="clave_login" name="clave" required autocomplete="current-password">
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg w-100">Entrar</button>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="registro" role="tabpanel">
                            <form action="<?= htmlspecialchars(url('/usuario/registrar')) ?>" method="POST" class="needs-validation" novalidate data-gp-validate="register"
                                  data-gp-confirm data-gp-confirm-title="Crear cuenta" data-gp-confirm-body="¿Enviar el formulario y crear tu usuario como cliente?" data-gp-confirm-ok="Registrar">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="DNI" class="form-label gp-label-required">DNI / NIE</label>
                                        <input type="text" class="form-control" id="DNI" name="DNI" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nombre" class="form-label gp-label-required">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="apellido1" class="form-label gp-label-required">Primer apellido</label>
                                        <input type="text" class="form-control" id="apellido1" name="apellido1" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="apellido2" class="form-label">Segundo apellido</label>
                                        <input type="text" class="form-control" id="apellido2" name="apellido2">
                                    </div>
                                    <div class="col-12">
                                        <label for="email" class="form-label gp-label-required">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="clave" class="form-label gp-label-required">Contraseña</label>
                                        <input type="password" class="form-control" id="clave" name="clave" required minlength="8" autocomplete="new-password">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg w-100 mt-4">Crear cuenta</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

            <p class="text-center text-muted small mt-4 mb-0">
                ¿Solo mirando? <a href="<?= htmlspecialchars(url('/pago')) ?>" class="link-light link-underline-opacity-25">Ver planes</a>
            </p>
        </div>
    </div>
</div>

<?php if (isset($successMsg)): ?>
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title">Listo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= $successMsg ?>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();
});
</script>
<?php endif; ?>

<?php if (isset($errorMsg)): ?>
<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title">Algo salió mal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?= $errorMsg ?>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
    errorModal.show();
});
</script>
<?php endif; ?>
