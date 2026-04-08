
        <div class="row mb-3">
            <div class="col-12">
                <a href="/proyecto-gym/inicioAdmin" class="btn btn-secondary">Volver</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Crear Monitor</h2>

                        <form action="/proyecto-gym/admin/crearMonitor" method="POST">

                            <div class="mb-3">
                                <label for="DNI" class="form-label">DNI:</label>
                                <input type="text" class="form-control" id="DNI" name="DNI" required>
                            </div>

                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>

                            <div class="mb-3">
                                <label for="apellido1" class="form-label">Apellido 1:</label>
                                <input type="text" class="form-control" id="apellido1" name="apellido1" required>
                            </div>

                            <div class="mb-3">
                                <label for="apellido2" class="form-label">Apellido 2:</label>
                                <input type="text" class="form-control" id="apellido2" name="apellido2">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="clave" class="form-label">Contraseña:</label>
                                <input type="password" class="form-control" id="clave" name="clave" required>
                            </div>

                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono:</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" required>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label for="especialidad" class="form-label">Especialidad:</label>
                                <input type="text" class="form-control" id="especialidad" name="especialidad" required>
                            </div>

                            <div class="mb-3">
                                <label for="disponibilidad" class="form-label">Disponibilidad:</label>
                                <input type="text" class="form-control" id="disponibilidad" name="disponibilidad" placeholder="Ej: mañanas, tardes..." required>
                            </div>

                            <button type="submit" class="btn btn-dark w-100">Crear Monitor</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
