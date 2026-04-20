<div class="content-wrapper">
    <div class="container mt-5">
        <div class="card shadow">

            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Crear Actividad</h4>
            </div>

            <div class="card-body">
                <form action="/proyecto-gym/admin/actividades/crear" method="post">

                    <!-- Nombre -->
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion"></textarea>
                    </div>

                    <!-- Duración -->
                    <div class="mb-3">
                        <label class="form-label">Duración (minutos)</label>
                        <input type="number" class="form-control" name="duracion" required>
                    </div>

                    <!-- Día de la semana -->
                    <div class="mb-3">
                        <label class="form-label">Día de la semana</label>
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
                        <label class="form-label">Hora de inicio</label>
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
                        <label class="form-label">Monitor</label>
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