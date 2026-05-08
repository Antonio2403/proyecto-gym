<div class="content-wrapper">

        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="mb-4">Crear Nueva Sala</h1>
                <p class="gp-form-required-legend text-muted small mb-3">Los campos con <span class="text-danger fw-bold" aria-hidden="true">*</span> son obligatorios.</p>
                <form action="<?= htmlspecialchars(url('/monitor/salas/crear')) ?>" method="POST" class="needs-validation" novalidate data-gp-validate="salaCreate"
                      data-gp-confirm data-gp-confirm-title="Crear sala" data-gp-confirm-body="¿Crear esta sala en el centro?" data-gp-confirm-ok="Crear">
                    <div class="mb-3">
                        <label for="nombre" class="form-label gp-label-required">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacidad" class="form-label gp-label-required">Capacidad</label>
                        <input type="number" class="form-control" id="capacidad" name="capacidad" required min="1" max="10000" step="1">
                    </div>
                    <div class="mb-3">
                        <label for="disponibilidad" class="form-label gp-label-required">Disponibilidad</label>
                        <select class="form-control" id="disponibilidad" name="disponibilidad" required>
                            <option value="">Seleccionar...</option>
                            <option value="L">Libre</option>
                            <option value="U">En uso</option>
                            <option value="R">Reservada</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Crear Sala</button>
                </form>
            </div>
        </div>
    </div>
