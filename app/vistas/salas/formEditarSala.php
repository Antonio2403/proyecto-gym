<div class="content-wrapper">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="mb-4">Editar Sala</h1>
            <form action="/proyecto-gym/monitor/salas/editar/<?php echo htmlspecialchars($sala['id']); ?>" method="POST">
                <div class="mb-3">
                    <label for="id" class="form-label">ID:</label>
                    <input type="text" class="form-control" id="id" name="id" value="<?php echo htmlspecialchars($sala['id']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($sala['nombre']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="capacidad" class="form-label">Capacidad:</label>
                    <input type="number" class="form-control" id="capacidad" name="capacidad" value="<?php echo htmlspecialchars($sala['capacidad']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="disponibilidad" class="form-label">Disponibilidad:</label>
                    <input type="text" class="form-control" id="disponibilidad" name="disponibilidad" value="<?php echo htmlspecialchars($sala['disponibilidad']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Guardar Cambios</button>
                <a href="/proyecto-gym/monitor/verSalas" class="btn btn-secondary w-100 mt-2">Cancelar</a>
            </form>
        </div>
    </div>
</div>