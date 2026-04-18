<div class="content-wrapper">
<div class="container mt-5">
    <div class="card shadow">

        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">Editar Actividad</h4>
        </div>

        <div class="card-body">
            <form action="/proyecto-gym/admin/actividades/editar/<?= $actividad['id'] ?>" method="post">

                <!-- ID oculto -->
                <input type="hidden" name="id" value="<?= $actividad['id']; ?>">

                <!-- Nombre -->
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre de la Actividad</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="nombre" 
                        name="nombre" 
                        value="<?= $actividad['nombre']; ?>" 
                        required
                    >
                </div>

                <!-- Descripción -->
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea 
                        class="form-control" 
                        id="descripcion" 
                        name="descripcion" 
                        rows="3"
                    ><?= $actividad['descripcion']; ?></textarea>
                </div>

                <!-- Sala -->
                <div class="mb-3">
                    <label for="sala" class="form-label">Sala</label>
                    <select class="form-select" id="sala" name="sala_id" required>
                        <option value="">Seleccione una sala</option>
                        <?php foreach ($salas as $sala): ?>
                            <option 
                                value="<?= $sala['id']; ?>"
                                <?= ($sala['id'] == $actividad['sala_id']) ? 'selected' : ''; ?>
                            >
                                <?= $sala['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Monitor -->
                <div class="mb-3">
                    <label for="monitor" class="form-label">Monitor</label>
                    <select class="form-select" id="monitor" name="monitor_id" required>
                        <option value="">Seleccione un monitor</option>
                        <?php foreach ($monitores as $monitor): ?>
                            <option 
                                value="<?= $monitor['id']; ?>"
                                <?= ($monitor['id'] == $actividad['monitor_id']) ? 'selected' : ''; ?>
                            >
                                <?= $monitor['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Botones -->
                <div class="d-flex justify-content-between">
                    <a href="/admin/actividades" class="btn btn-secondary">
                        Volver
                    </a>

                    <button type="submit" class="btn btn-warning">
                        Actualizar Actividad
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>

</div>