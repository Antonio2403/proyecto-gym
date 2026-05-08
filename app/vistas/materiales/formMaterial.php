<div class="content-wrapper">

    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="mb-4">Registrar Nuevo Material</h1>
            <p class="gp-form-required-legend text-muted small mb-3">Los campos con <span class="text-danger fw-bold" aria-hidden="true">*</span> son obligatorios.</p>
            <form method="POST" action="<?= htmlspecialchars(url('/monitor/salas/' . $sala_id . '/materiales/crear')) ?>" class="needs-validation" novalidate data-gp-validate="materialCreate"
                  data-gp-confirm data-gp-confirm-title="Registrar material" data-gp-confirm-body="¿Dar de alta este material en la sala?" data-gp-confirm-ok="Registrar">
                <div class="mb-3">
                    <label for="nombre" class="form-label gp-label-required">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="200">
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label gp-label-required">Estado</label>
                    <select class="form-control" id="estado" name="estado" required>
                        <option value="">Seleccionar estado</option>
                        <option value="B">Buen estado</option>
                        <option value="M">Mal estado</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Registrar Material</button>
            </form>
        </div>
    </div>
</div>