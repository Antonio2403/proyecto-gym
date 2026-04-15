<div class="content-wrapper">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="mb-4">Editar material</h1>
            <form method="POST" action="/proyecto-gym/monitor/salas/<?= $sala_id ?>/materiales/editar/<?php echo htmlspecialchars($material['id']); ?>">
                <div class="mb-3">
                    <label for="id" class="form-label">ID:</label>
                    <input type="text" class="form-control" id="id" name="id" value="<?php echo htmlspecialchars($material['id']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($material['nombre']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">estado:</label>
                    <input type="text" class="form-control" id="estado" name="estado" value="<?php echo htmlspecialchars($material['estado']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Guardar Cambios</button>
                <a href="/proyecto-gym/monitor/verSalas" class="btn btn-secondary w-100 mt-2">Cancelar</a>
            </form>
        </div>
    </div>
</div>