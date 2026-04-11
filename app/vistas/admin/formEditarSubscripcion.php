<div class="content-wrapper">

    <a href="gestionSubscripciones" class="btn btn-secondary mb-3">Volver</a>
    <h2 class="mb-4">Editar Subscripción</h2>
    
    <form action="editarSubscripcion" method="POST" class="w-50">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($subscripcion['id']); ?>">
        
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($subscripcion['nombre']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="precio" class="form-label">Precio:</label>
            <input type="number" id="precio" name="precio" step="0.01" class="form-control" value="<?php echo htmlspecialchars($subscripcion['precio']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="duracion" class="form-label">Duración (en meses):</label>
            <input type="number" id="duracion" name="duracion" class="form-control" value="<?php echo htmlspecialchars($subscripcion['duracion']); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Subscripción</button>
    </form>

</div>