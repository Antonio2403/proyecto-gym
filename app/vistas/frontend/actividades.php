<?php
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /proyecto-gym/login?error=Debes iniciar sesión para ver las actividades');
    exit;
}
?>
<div class="container mt-4">
    <h3 class="text-center mb-4">Horario de Actividades</h3>
    <?php if (isset($_GET['error'])): ?>
        <?php $errorMsg = htmlspecialchars($_GET['error']); ?>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <?php $succsessMsg = htmlspecialchars($_GET['success']); ?>
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
                $horas = range(8, 22);

                $dias = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];

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

<!-- MODAL ERROR -->
<?php if (isset($errorMsg)): ?>
    <div class="modal fade" id="errorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                    <?= $errorMsg ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        });
    </script>
<?php endif; ?>


<!-- MODAL SUCCESS -->
<?php if (isset($succsessMsg)): ?>
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Éxito</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                    <?= $succsessMsg ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        });
    </script>
<?php endif; ?>