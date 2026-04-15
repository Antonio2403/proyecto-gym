<div class="content-wrapper">

    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="mb-4">Registrar Nuevo Material</h1>
            <form method="POST" action="/proyecto-gym/monitor/salas/<?= $sala_id ?>/materiales/crear">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">estado:</label>
                    <input type="text" class="form-control" id="estado" name="estado" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Registrar Material</button>
            </form>
        </div>
    </div>
</div>