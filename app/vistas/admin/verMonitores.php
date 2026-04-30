<div class="content-wrapper">
    <div class="container mt-4">

        <!-- BOTÓN -->
        <div class="mb-3">
            <a href="/proyecto-gym/admin/registrarMonitor" class="btn btn-primary">
                + Nuevo Monitor
            </a>
        </div>

        <h2 class="mb-4">Gestión de Monitores</h2>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Especialidad</th>
                        <th>Disponibilidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($monitores)): ?>
                        <?php foreach ($monitores as $monitor): ?>

                            <tr>
                                <td><?= $monitor['monitor_id'] ?></td>

                                <td><?= $monitor['DNI'] ?></td>

                                <td><?= $monitor['nombre'] ?></td>

                                <td>
                                    <?= $monitor['apellido1'] ?>
                                    <?= $monitor['apellido2'] ?>
                                </td>

                                <td><?= $monitor['email'] ?></td>

                                <td><?= $monitor['telefono'] ?></td>

                                <td><?= $monitor['especialidad'] ?></td>

                                <td><?= $monitor['disponibilidad'] ?></td>

                                <!-- ACCIONES -->
                                <td>
                                    <a href="/proyecto-gym/admin/monitores/editar/<?= $monitor['monitor_id'] ?>"
                                        class="btn btn-warning btn-sm">
                                        Editar
                                    </a>

                                    <a href="/proyecto-gym/admin/monitores/eliminar/<?= $monitor['monitor_id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Eliminar este monitor?')">
                                        Eliminar
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">
                                No hay monitores registrados.
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>

            </table>
        </div>

    </div>
</div>