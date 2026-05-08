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
                    <h2 class="card-title mb-4">Editar Monitor</h2>

                    <p class="gp-form-required-legend text-muted mb-3">Los campos con <span class="text-danger fw-bold" aria-hidden="true">*</span> son obligatorios.</p>

                    <form action="<?= htmlspecialchars(url('/admin/monitores/editar')) ?>" method="POST" class="needs-validation" novalidate data-gp-validate="monitorEdit"
                          data-gp-confirm data-gp-confirm-title="Guardar monitor" data-gp-confirm-body="¿Guardar los cambios de este monitor?" data-gp-confirm-ok="Guardar">

                        <!-- ID OCULTO -->
                        <input type="hidden" name="id" value="<?= $monitor['monitor_id'] ?>">

                        <!-- DNI -->
                        <div class="mb-3">
                            <label for="DNI" class="form-label gp-label-required">DNI / NIE</label>
                            <input type="text" class="form-control" name="DNI"
                                value="<?= $monitor['DNI'] ?>" required>
                        </div>

                        <!-- Nombre -->
                        <div class="mb-3">
                            <label class="form-label gp-label-required">Nombre</label>
                            <input type="text" class="form-control" name="nombre"
                                value="<?= $monitor['nombre'] ?>" required>
                        </div>

                        <!-- Apellido 1 -->
                        <div class="mb-3">
                            <label class="form-label gp-label-required">Primer apellido</label>
                            <input type="text" class="form-control" name="apellido1"
                                value="<?= $monitor['apellido1'] ?>" required>
                        </div>

                        <!-- Apellido 2 -->
                        <div class="mb-3">
                            <label class="form-label">Apellido 2:</label>
                            <input type="text" class="form-control" name="apellido2"
                                value="<?= $monitor['apellido2'] ?>">
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label gp-label-required">Email</label>
                            <input type="email" class="form-control" name="email"
                                value="<?= $monitor['email'] ?>" required>
                        </div>

                        <!-- Contraseña -->
                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña:</label>
                            <input type="password" class="form-control" name="clave">
                            <small class="text-muted">
                                Déjalo vacío si no quieres cambiarla
                            </small>
                        </div>

                        <!-- Teléfono -->
                        <div class="mb-3">
                            <label class="form-label gp-label-required">Teléfono</label>
                            <input type="text" class="form-control" name="telefono"
                                value="<?= $monitor['telefono'] ?>" required>
                        </div>

                        <hr>

                        <!-- Especialidad -->
                        <div class="mb-3">
                            <label class="form-label gp-label-required">Especialidad</label>
                            <input type="text" class="form-control" name="especialidad"
                                value="<?= $monitor['especialidad'] ?>" required>
                        </div>

                        <!-- Disponibilidad -->
                        <div class="mb-3">
                            <label class="form-label gp-label-required">Disponibilidad</label>
                            <input type="text" class="form-control" name="disponibilidad"
                                value="<?= $monitor['disponibilidad'] ?>" required>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">
                            Actualizar Monitor
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>