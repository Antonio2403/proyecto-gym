<div class="container mt-4">
    <h3 class="text-center mb-4">Horario de Actividades</h3>
    <!-- ALERTAS -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center">
            Te has apuntado correctamente a la actividad
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger text-center">
            Error al apuntarse a la actividad
        </div>
    <?php endif; ?>

    <a href="/proyecto-gym/usuario/inscripciones/mis-inscripciones" class="btn btn-primary mb-3">Ver mis inscripciones</a>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">

            <thead class="table-dark">
                <tr>
                    <th>Hora</th>
                    <th>Lunes</th>
                    <th>Martes</th>
                    <th>Miércoles</th>
                    <th>Jueves</th>
                    <th>Viernes</th>
                    <th>Sábado</th>
                    <th>Domingo</th>
                </tr>
            </thead>

            <tbody>

                <?php
                // Horas del día (puedes ajustar)
                $horas = range(8, 22);

                // Mapa días
                $dias = ['L','M','X','J','V','S','D'];

                foreach ($horas as $hora):
                ?>
                    <tr>
                        <td><strong><?= sprintf("%02d:00", $hora) ?></strong></td>

                        <?php foreach ($dias as $dia): ?>
                            <td>
                                <?php
                                foreach ($actividades as $act):

                                    $horaActividad = date('H', strtotime($act['fecha_inicio']));

                                    if ($act['dia_semana'] == $dia && $horaActividad == $hora):
                                ?>

                                        <div class="p-2 bg-primary text-white rounded mb-1">
                                            <strong><?= $act['nombre'] ?></strong><br>
                                            <small><?= date('H:i', strtotime($act['fecha_inicio'])) ?></small>
                                        </div>

                                        <form method="post" action="/proyecto-gym/usuario/inscripciones/apuntarse">
                                            <input type="hidden" name="actividad_id" value="<?= $act['id'] ?>">
                                            <button class="btn btn-sm btn-success">Apuntarse</button>
                                        </form>

                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </td>
                        <?php endforeach; ?>

                    </tr>
                <?php endforeach; ?>

            </tbody>

        </table>
    </div>
</div>