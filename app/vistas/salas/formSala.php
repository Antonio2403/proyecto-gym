<div class="content-wrapper">

        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="mb-4">Crear Nueva Sala</h1>
                <form action="/proyecto-gym/monitor/salas/crear" method="POST">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacidad" class="form-label">Capacidad:</label>
                        <input type="number" class="form-control" id="capacidad" name="capacidad" required>
                    </div>
                    <div class="mb-3">
                        <label for="disponibilidad" class="form-label">Disponibilidad:</label>
                        <input type="text" class="form-control" id="disponibilidad" name="disponibilidad" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Crear Sala</button>
                </form>
            </div>
        </div>
    </div>
