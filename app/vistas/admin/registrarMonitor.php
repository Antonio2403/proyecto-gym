<div class="content-wrapper">

        <div class="row mb-3">
            <div class="col-12">
                <a href="<?= htmlspecialchars(url('/admin/verMonitores')) ?>" class="btn btn-secondary">Volver</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Crear Monitor</h2>

                        <?php if (!empty($_GET['error'])): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars((string) $_GET['error']) ?></div>
                        <?php endif; ?>

                        <p class="gp-form-required-legend text-muted mb-3">Los campos con <span class="text-danger fw-bold" aria-hidden="true">*</span> son obligatorios.</p>

                        <form action="<?= htmlspecialchars(url('/admin/crearMonitor')) ?>" method="POST" class="needs-validation" novalidate data-gp-validate="monitorCreate"
                              data-gp-confirm data-gp-confirm-title="Crear monitor" data-gp-confirm-body="¿Crear esta cuenta de monitor?" data-gp-confirm-ok="Crear">

                            <div class="mb-3">
                                <label for="DNI" class="form-label gp-label-required">DNI / NIE</label>
                                <input type="text" class="form-control" id="DNI" name="DNI" required>
                            </div>

                            <div class="mb-3">
                                <label for="nombre" class="form-label gp-label-required">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>

                            <div class="mb-3">
                                <label for="apellido1" class="form-label gp-label-required">Primer apellido</label>
                                <input type="text" class="form-control" id="apellido1" name="apellido1" required>
                            </div>

                            <div class="mb-3">
                                <label for="apellido2" class="form-label">Apellido 2:</label>
                                <input type="text" class="form-control" id="apellido2" name="apellido2">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label gp-label-required">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="clave" class="form-label gp-label-required">Contraseña</label>
                                <input type="password" class="form-control" id="clave" name="clave" required minlength="8" autocomplete="new-password">
                            </div>

                            <div class="mb-3">
                                <label for="telefono" class="form-label gp-label-required">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" required>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label for="especialidad" class="form-label gp-label-required">Especialidad</label>
                                <input type="text" class="form-control" id="especialidad" name="especialidad" required>
                            </div>

                            <div class="mb-3">
                                <label for="disponibilidad" class="form-label gp-label-required">Disponibilidad</label>
                                <input type="text" class="form-control" id="disponibilidad" name="disponibilidad" placeholder="Ej: mañanas, tardes..." required>
                            </div>

                            <button type="submit" class="btn btn-dark w-100">Crear Monitor</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
