<a href="verMonitorSolicitudes" class="btn btn-secondary btn-sm mb-3">Volver</a>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Crear Solicitud</h5>
                    <form action="/proyecto-gym/monitor/crearSolicitud" method="POST">
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo de solicitud:</label>
                            <input type="text" class="form-control" id="tipo" name="tipo" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Enviar solicitud</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
