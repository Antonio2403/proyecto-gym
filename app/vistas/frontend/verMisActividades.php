<div class="container mt-4">
    <a href="/proyecto-gym/usuario/actividades" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Volver al horario
    </a>

    <h3 class="text-center mb-4">Mis Actividades</h3>

    <!-- ALERTAS -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center">
            Inscripción cancelada correctamente
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger text-center">
            Error al cancelar la inscripción
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center align-middle">

            <thead class="table-dark">
                <tr>
                    <th>Actividad</th>
                    <th>Día</th>
                    <th>Hora</th>
                    <th>Sala</th>
                    <th>Monitor</th>
                    <th>Acción</th>
                </tr>
            </thead>

            <tbody>

                <?php if (!empty($inscripciones)): ?>
                    
                    <?php foreach ($inscripciones as $ins): ?>

                        <tr>
                            <td><?= $ins['actividad'] ?></td>

                            <!-- Día bonito -->
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
                                echo $dias[$ins['dia_semana']] ?? $ins['dia_semana'];
                                ?>
                            </td>

                            <!-- Hora -->
                            <td>
                                <?= date('H:i', strtotime($ins['fecha_inicio'])) ?>
                                -
                                <?= date('H:i', strtotime($ins['fecha_fin'])) ?>
                            </td>

                            <td><?= $ins['sala'] ?></td>
                            <td><?= $ins['monitor'] ?></td>

                            <!-- BOTÓN CANCELAR -->
                            <td>
                                <form method="post" action="/proyecto-gym/usuario/inscripciones/cancelar">
                                    <input type="hidden" name="inscripcion_id" value="<?= $ins['id'] ?>">

                                    <button 
                                        type="submit" 
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Cancelar esta inscripción?')"
                                    >
                                        Cancelar
                                    </button>
                                </form>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="text-center">
                            No estás inscrito en ninguna actividad
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>
    </div>

</div>