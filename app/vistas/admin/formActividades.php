<div class="content-wrapper">
    <div class="container mt-5">
        <div class="card shadow">

            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Crear Actividad</h4>
            </div>

            <div class="card-body">
                <p class="gp-form-required-legend text-muted mb-3">Los campos con <span class="text-danger fw-bold" aria-hidden="true">*</span> son obligatorios.</p>
                <form action="<?= htmlspecialchars(url('/admin/actividades/crear')) ?>" method="post" class="needs-validation" novalidate data-gp-validate="activityCreate"
                      data-gp-confirm data-gp-confirm-title="Crear actividad" data-gp-confirm-body="¿Crear esta actividad en el horario?" data-gp-confirm-ok="Crear">

                    <!-- Nombre -->
                    <div class="mb-3">
                        <label class="form-label gp-label-required">Nombre</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion"></textarea>
                    </div>

                    <!-- Duración -->
                    <div class="mb-3">
                        <label class="form-label gp-label-required">Duración (minutos)</label>
                        <input type="number" class="form-control" name="duracion" required min="1" max="600" step="1">
                    </div>

                    <!-- Día de la semana -->
                    <div class="mb-3">
                        <label class="form-label gp-label-required">Día de la semana</label>
                        <select class="form-select" name="dia_semana" required>
                            <option value="">Seleccione</option>
                            <option value="L">Lunes</option>
                            <option value="M">Martes</option>
                            <option value="X">Miércoles</option>
                            <option value="J">Jueves</option>
                            <option value="V">Viernes</option>
                            <option value="S">Sábado</option>
                            <option value="D">Domingo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label gp-label-required">Hora de inicio</label>
                        <input type="time" class="form-control" name="hora_inicio" required>
                    </div>

                    <!-- Sala -->
                    <div class="mb-3">
                        <label class="form-label">Sala</label>
                        <select class="form-select" name="sala_id" required>
                            <option value="">Seleccione una sala</option>
                            <?php foreach ($salas as $sala): ?>
                                <option value="<?= $sala['id']; ?>">
                                    <?= $sala['nombre']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Monitor -->
                    <div class="mb-3">
                        <label class="form-label gp-label-required">Monitor</label>
                        <select class="form-select" name="monitor_id" required>
                            <option value="">Seleccione un monitor</option>
                            <?php foreach ($monitores as $monitor): ?>
                                <option value="<?= $monitor['id']; ?>">
                                    <?= $monitor['nombre']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Botón -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            Guardar Actividad
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>