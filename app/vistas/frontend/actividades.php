<?php
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . url('/login') . '?error=' . rawurlencode('Debes iniciar sesión para ver las actividades'));
    exit;
}
$semanaOffset = (int) ($semana_offset ?? 0);
$weekLabel = (string) ($week_label ?? '');
$inscritosPorActividad = is_array($inscritos_por_actividad ?? null)
    ? $inscritos_por_actividad
    : [];
$scheduleWeekMonday = isset($schedule_week_monday) ? (string) $schedule_week_monday : '';
?>
<div class="gp-schedule-page py-4 py-lg-5">
    <div class="container-fluid px-lg-5">
        <div class="gp-schedule-panel shadow-sm">
            <header class="gp-schedule-head text-center py-4 px-3">
                <h1 class="gp-schedule-title mb-2">Horario de Actividades</h1>
                <?php if ($weekLabel !== ''): ?>
                    <p class="gp-schedule-subtitle mb-0"><?= htmlspecialchars($weekLabel) ?></p>
                <?php endif; ?>
            </header>

            <?php if (isset($_GET['error'])): ?>
                <?php $errorMsg = htmlspecialchars((string) $_GET['error']); ?>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <?php $succsessMsg = htmlspecialchars((string) $_GET['success']); ?>
            <?php endif; ?>

            <div class="gp-schedule-toolbar px-3 px-md-4 pb-3">
                <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-between gap-3">
                    <a href="<?= htmlspecialchars(url('/usuario/inscripciones/mis-inscripciones')) ?>"
                       class="btn gp-btn-orange fw-semibold px-4 py-2">
                        Ver mis inscripciones
                    </a>
                    <div class="gp-week-toggle btn-group" role="group" aria-label="Cambiar semana">
                        <a href="<?= htmlspecialchars(url('/usuario/actividades') . '?semana=0') ?>"
                           class="btn <?= $semanaOffset === 0 ? 'active' : '' ?>">Semana actual</a>
                        <a href="<?= htmlspecialchars(url('/usuario/actividades') . '?semana=1') ?>"
                           class="btn <?= $semanaOffset === 1 ? 'active' : '' ?>">Semana siguiente</a>
                    </div>
                </div>
            </div>

            <div class="table-responsive gp-schedule-table-wrap px-2 px-md-4 pb-4">
                <table class="table table-bordered gp-schedule-grid mb-0 text-center align-middle">
                    <thead>
                        <tr>
                            <th class="gp-schedule-col-hour" scope="col">Hora</th>
                            <th scope="col">Lun</th>
                            <th scope="col">Mar</th>
                            <th scope="col">Mié</th>
                            <th scope="col">Jue</th>
                            <th scope="col">Vie</th>
                            <th scope="col">Sáb</th>
                            <th scope="col">Dom</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $horas = range(8, 22);
                        $dias = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
                        foreach ($horas as $hora):
                        ?>
                            <tr class="gp-schedule-row">
                                <th class="gp-schedule-hour-cell fw-semibold" scope="row"><?= sprintf('%02d:00', $hora) ?></th>
                                <?php foreach ($dias as $dia): ?>
                                    <td class="gp-schedule-cell align-top">
                                        <?php
                                        foreach ($actividades as $act):
                                            $horaActividad = date('H', strtotime($act['fecha_inicio']));
                                            if ($act['dia_semana'] == $dia && (int) $horaActividad === $hora):
                                                $actId = (int) $act['id'];
                                                $plazas = max(1, (int) ($act['plazas'] ?? 20));
                                                $inscritos = (int) ($inscritosPorActividad[$actId] ?? 0);
                                                $tplId = 'gp-act-pop-' . $actId . '-' . $dia . '-' . $hora;
                                                $desc = trim((string) ($act['descripcion'] ?? ''));
                                                $monitorNombre = trim((string) ($act['monitor_nombre'] ?? ''));
                                                $salaNombre = trim((string) ($act['sala_nombre'] ?? ''));
                                                $duracion = (int) ($act['duracion'] ?? 0);
                                                $horaIni = date('H:i', strtotime($act['fecha_inicio']));
                                                $fechaUnica = '';
                                                if ((int) ($act['recurrente'] ?? 1) === 0 && !empty($act['fecha_inicio'])) {
                                                    $fechaUnica = date('d/m/Y', strtotime($act['fecha_inicio']));
                                                }
                                                $cupoLleno = $inscritos >= $plazas;
                                                $fechaComentarios = '';
                                                if ($scheduleWeekMonday !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $scheduleWeekMonday)) {
                                                    if ((int) ($act['recurrente'] ?? 1) === 1) {
                                                        $offsetDia = ['L' => 0, 'M' => 1, 'X' => 2, 'J' => 3, 'V' => 4, 'S' => 5, 'D' => 6];
                                                        $delta = $offsetDia[$dia] ?? 0;
                                                        $fechaComentarios = (new DateTimeImmutable($scheduleWeekMonday))->modify('+' . $delta . ' days')->format('Y-m-d');
                                                    } elseif (!empty($act['fecha_inicio'])) {
                                                        $fechaComentarios = substr((string) $act['fecha_inicio'], 0, 10);
                                                    }
                                                }
                                                $urlOpiniones = '';
                                                if ($fechaComentarios !== '') {
                                                    $urlOpiniones = url('/usuario/actividades/sesion/comentarios') . '?' . http_build_query([
                                                        'actividad_id' => $actId,
                                                        'fecha' => $fechaComentarios,
                                                        'orden' => 'desc',
                                                    ]);
                                                }
                                        ?>
                                                <template id="<?= htmlspecialchars($tplId) ?>">
                                                    <div class="gp-schedule-popover-inner text-start">
                                                        <?php if ($desc !== ''): ?>
                                                            <p class="gp-schedule-pop-desc mb-2"><?= nl2br(htmlspecialchars($desc)) ?></p>
                                                        <?php endif; ?>
                                                        <ul class="gp-schedule-pop-list list-unstyled mb-0 small">
                                                            <?php if ($fechaUnica !== ''): ?>
                                                                <li><span class="text-muted">Fecha</span><br><strong><?= htmlspecialchars($fechaUnica) ?></strong></li>
                                                            <?php endif; ?>
                                                            <li><span class="text-muted">Horario</span><br><strong><?= htmlspecialchars($horaIni) ?></strong><?= $duracion > 0 ? ' · ' . (int) $duracion . ' min' : '' ?></li>
                                                            <li><span class="text-muted">Monitor</span><br><strong><?= htmlspecialchars($monitorNombre !== '' ? $monitorNombre : 'Sin asignar') ?></strong></li>
                                                            <li><span class="text-muted">Sala</span><br><strong><?= htmlspecialchars($salaNombre !== '' ? $salaNombre : '—') ?></strong></li>
                                                            <li><span class="text-muted">Ocupación</span><br><strong><?= (int) $inscritos ?> / <?= (int) $plazas ?></strong> personas</li>
                                                        </ul>
                                                    </div>
                                                </template>
                                                <div class="gp-schedule-act mb-2<?= $cupoLleno ? ' gp-schedule-act--full' : '' ?>"
                                                     tabindex="0"
                                                     role="button"
                                                     data-gp-popover-id="<?= htmlspecialchars($tplId) ?>"
                                                     data-popover-title="<?= htmlspecialchars((string) $act['nombre']) ?>"
                                                     aria-label="Detalles de la actividad: <?= htmlspecialchars((string) $act['nombre']) ?>">
                                                    <div class="gp-schedule-act-body text-start">
                                                        <strong class="d-block text-white"><?= htmlspecialchars((string) $act['nombre']) ?></strong>
                                                        <span class="gp-schedule-act-time"><?= htmlspecialchars($horaIni) ?></span>
                                                        <span class="gp-schedule-act-capacity" title="Personas apuntadas / plazas totales"><?= (int) $inscritos ?> / <?= (int) $plazas ?></span>
                                                    </div>
                                                </div>
                                                <form method="post" action="<?= htmlspecialchars(url('/usuario/inscripciones/apuntarse')) ?>" class="mb-1"
                                                      data-gp-confirm
                                                      data-gp-confirm-title="Reservar plaza"
                                                      data-gp-confirm-body="¿Confirmar tu apuntamiento a esta actividad en el horario mostrado?"
                                                      data-gp-confirm-ok="Sí, reservar">
                                                    <input type="hidden" name="actividad_id" value="<?= $actId ?>">
                                                    <button type="submit" class="btn gp-btn-apuntarse btn-sm w-100"<?= $cupoLleno ? ' disabled' : '' ?>><?= $cupoLleno ? 'Completo' : 'Apuntarse' ?></button>
                                                </form>
                                                <?php if ($urlOpiniones !== ''): ?>
                                                    <a href="<?= htmlspecialchars($urlOpiniones) ?>" class="gp-schedule-opiniones d-block btn btn-outline-light btn-sm w-100 py-1 lh-sm small text-decoration-none">Opiniones</a>
                                                <?php endif; ?>
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

        <p class="text-center mt-3 mb-0">
            <a href="<?= htmlspecialchars(url('/inicio')) ?>" class="gp-schedule-backlink small">← Volver al inicio</a>
        </p>
    </div>
</div>

<?php if (isset($errorMsg)): ?>
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content gp-modal-dark">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Error</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center"><?= $errorMsg ?></div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Modal(document.getElementById('errorModal')).show();
        });
    </script>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof bootstrap === 'undefined' || !bootstrap.Popover) {
            return;
        }
        document.querySelectorAll('.gp-schedule-act[data-gp-popover-id]').forEach(function (el) {
            var tid = el.getAttribute('data-gp-popover-id');
            var tpl = document.getElementById(tid);
            if (!tpl) {
                return;
            }
            new bootstrap.Popover(el, {
                html: true,
                sanitize: false,
                trigger: 'hover focus',
                placement: 'auto',
                container: 'body',
                title: el.getAttribute('data-popover-title') || '',
                content: tpl.innerHTML,
                customClass: 'gp-schedule-popover-bs'
            });
        });
    });
</script>

<?php if (isset($succsessMsg)): ?>
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content gp-modal-dark">
                <div class="modal-header border-0 bg-success bg-opacity-25">
                    <h5 class="modal-title">Correcto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center"><?= $succsessMsg ?></div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Genial</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Modal(document.getElementById('successModal')).show();
        });
    </script>
<?php endif; ?>
