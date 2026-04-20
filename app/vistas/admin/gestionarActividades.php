<div class="content-wrapper">
    <div class="container mt-4">

        <!-- BOTÓN -->
        <div class="mb-3">
            <a href="/proyecto-gym/admin/actividades/crear" class="btn btn-primary">
                + Nueva Actividad
            </a>
        </div>

        <h2 class="mb-4">Gestión de Actividades</h2>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Duración</th>
                        <th>Día</th>
                        <th>Hora Inicio</th>
                        <th>Hora Fin</th>
                        <th>Sala</th>
                        <th>Monitor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($actividades)): ?>
                        <?php foreach ($actividades as $actividad): ?>

                            <tr>
                                <td><?= $actividad['id'] ?></td>

                                <td><?= $actividad['nombre'] ?></td>

                                <td><?= $actividad['descripcion'] ?></td>

                                <td><?= $actividad['duracion'] ?> min</td>

                                <!-- Día -->
                                <td>
                                    <?php
                                    $dias = [
                                        'L' => 'Lunes',
                                        'M' => 'Martes',
                                        'X' => 'Miércoles',
                                        'J' => 'Jueves',
                                        'V' => 'Viernes',
                                        'S' => 'Sábado',
                                        'D' => 'Domingo'
                                    ];
                                    echo $dias[$actividad['dia_semana']] ?? $actividad['dia_semana'];
                                    ?>
                                </td>

                                <!-- Horas -->
                                <td><?= date('H:i', strtotime($actividad['fecha_inicio'])) ?></td>
                                <td><?= date('H:i', strtotime($actividad['fecha_fin'])) ?></td>

                                <!-- IDs (luego puedes hacer JOIN) -->
                                <td><?= $actividad['sala_nombre'] ?></td>
                                <td><?= $actividad['monitor_nombre'] ?></td>

                                <!-- ACCIONES -->
                                <td>
                                    <a href="/proyecto-gym/admin/actividades/editar/<?= $actividad['id'] ?>"
                                        class="btn btn-warning btn-sm">
                                        Editar
                                    </a>

                                    <a href="/proyecto-gym/admin/actividades/eliminar/<?= $actividad['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Seguro que quieres eliminar esta actividad?')">
                                        Eliminar
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">
                                No hay actividades registradas aún.
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>

            </table>
        </div>

    </div>
</div>