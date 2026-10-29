<?php
$semanaOffset = (int) ($semana_offset ?? 0);
$weekLabel = (string) ($week_label ?? '');
$inscritosPorCelda = is_array($inscritos_por_celda ?? null)
    ? $inscritos_por_celda
    : [];
$sesionReservablePorCelda = is_array($sesion_reservable_por_celda ?? null)
    ? $sesion_reservable_por_celda
    : [];
$scheduleWeekMonday = isset($schedule_week_monday) ? (string) $schedule_week_monday : '';
$offsetDiaSemana = ['L' => 0, 'M' => 1, 'X' => 2, 'J' => 3, 'V' => 4, 'S' => 5, 'D' => 6];
$tienePlanActivo = !empty($tiene_plan_activo);
$esClienteSocio = !empty($es_cliente_socio);
$cupoResumen = is_array($cupo_resumen_semana ?? null) ? $cupo_resumen_semana : null;
$inscritoPorSesion = is_array($inscrito_por_sesion ?? null) ? $inscrito_por_sesion : [];
$puedeMasReservaSemana = !isset($puede_mas_reserva_semana) || $puede_mas_reserva_semana;
$clasesFiltro = [];
foreach ($actividades as $actFiltro) {
    $fid = (int) ($actFiltro['id'] ?? 0);
    if ($fid <= 0 || isset($clasesFiltro[$fid])) {
        continue;
    }
    $clasesFiltro[$fid] = [
        'id' => $fid,
        'nombre' => (string) ($actFiltro['nombre'] ?? 'Clase'),
        'color' => Actividad::normalizarColor((string) ($actFiltro['color'] ?? '')) ?? '#C45C26',
    ];
}
uasort($clasesFiltro, static function (array $a, array $b): int {
    return strcasecmp($a['nombre'], $b['nombre']);
});
?>
<div class="gp-schedule-page py-6 lg:py-12">
    <div class="relative w-full lg:px-12">
        <div class="gp-schedule-panel shadow-sm">
            <div class="gp-photo-frame" style="height: 9rem; border-radius: 0; border: 0;">
                <img class="gp-photo" src="<?= htmlspecialchars(asset('img/pixabay/equipo.jpg')) ?>"
                     width="1280" height="853" alt="Clase en grupo en la sala del gimnasio">
            </div>
            <header class="gp-schedule-head text-center py-6 px-4">
                <h1 class="gp-schedule-title mb-2">Horario de Actividades</h1>
                <?php if ($weekLabel !== ''): ?>
                    <p class="gp-schedule-subtitle mb-0"><?= htmlspecialchars($weekLabel) ?></p>
                <?php endif; ?>
            </header>

            <?php if (isset($_GET['error'])): ?>
                <?php $errorMsg = htmlspecialchars((string) $_GET['error']); ?>
            <?php endif; ?>
            <?php if (isset($_GET['info'])): ?>
                <?php $infoMsg = htmlspecialchars((string) $_GET['info']); ?>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <?php $succsessMsg = htmlspecialchars((string) $_GET['success']); ?>
            <?php endif; ?>

            <div class="gp-schedule-toolbar px-4 md:px-6 pb-4">
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                    <a href="<?= htmlspecialchars(url('/usuario/inscripciones/mis-inscripciones')) ?>"
                       class="btn gp-btn-orange font-semibold px-6 py-2">
                        Ver mis inscripciones
                    </a>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-4">
                        <?php if ($esClienteSocio): ?>
                            <?php if ($tienePlanActivo && $cupoResumen && (int) ($cupoResumen['max_semana'] ?? 0) > 0): ?>
                            <div class="gp-schedule-quota-hint text-center sm:text-left text-sm text-white/50 px-4 py-2 rounded-lg border border-slate-100/25 bg-slate-800/40"
                                 role="status"
                                 title="Reservas de esta semana según tu plan activo">
                                <span class="block text-white/50 uppercase text-sm mb-1" style="letter-spacing: .04em;">Tu plan esta semana</span>
                                <span class="text-white font-semibold"><?= (int) ($cupoResumen['usado'] ?? 0) ?></span>
                                <span class="text-white/50"> reservas usadas de </span>
                                <span class="text-white font-semibold"><?= (int) $cupoResumen['max_semana'] ?></span>
                                <?php if (isset($cupoResumen['restante'])): ?>
                                    <span class="text-white/50"> · te quedan </span>
                                    <span class="text-yellow-700 font-semibold"><?= (int) $cupoResumen['restante'] ?></span>
                                <?php endif; ?>
                            </div>
                            <?php elseif ($tienePlanActivo && $cupoResumen && (int) ($cupoResumen['max_semana'] ?? 0) <= 0): ?>
                            <div class="gp-schedule-quota-hint text-sm text-white/50 px-4 py-2 rounded-lg border border-slate-100/25 bg-slate-800/40" role="status">
                                <strong class="text-white">Plan activo:</strong> sin tope fijo de clases por semana en el sistema.
                            </div>
                            <?php else: ?>
                            <div class="gp-schedule-quota-hint text-sm px-4 py-2 rounded-lg border border-yellow-400/50 bg-slate-800/50 text-left" role="alert">
                                <strong class="text-yellow-700">Sin plan activo.</strong>
                                <span class="text-white/50"> No puedes reservar ni gestionar plazas hasta contratar o renovar en </span>
                                <a class="text-amber-700 underline font-semibold" href="<?= htmlspecialchars(url('/pago')) ?>">Planes</a>.
                                <span class="text-white/50 block mt-1 text-sm">Si tenías reservas antiguas, quedan congeladas hasta que vuelvas a tener un plan en vigor.</span>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <div class="gp-week-toggle btn-group" role="group" aria-label="Cambiar semana">
                            <a href="<?= htmlspecialchars(url('/usuario/actividades') . '?semana=0') ?>"
                               class="btn <?= $semanaOffset === 0 ? 'active' : '' ?>">Semana actual</a>
                            <a href="<?= htmlspecialchars(url('/usuario/actividades') . '?semana=1') ?>"
                               class="btn <?= $semanaOffset === 1 ? 'active' : '' ?>">Semana siguiente</a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($clasesFiltro !== []): ?>
            <div class="gp-schedule-filter px-4 md:px-6 pb-4" data-gp-class-filter>
                <label class="gp-schedule-filter__label" id="gp-class-filter-label">Filtrar clases</label>
                <div class="gp-schedule-filter__control">
                    <button type="button" class="gp-schedule-filter__btn" data-gp-class-filter-toggle
                            aria-haspopup="listbox" aria-expanded="false" aria-labelledby="gp-class-filter-label">
                        <span data-gp-class-filter-label>Todas las clases</span>
                    </button>
                    <div class="gp-schedule-filter__panel" data-gp-class-filter-panel hidden role="listbox" aria-multiselectable="true">
                        <?php foreach ($clasesFiltro as $clase): ?>
                            <label class="gp-schedule-filter__opt">
                                <input type="checkbox" value="<?= (int) $clase['id'] ?>" data-nombre="<?= htmlspecialchars($clase['nombre']) ?>">
                                <span class="gp-schedule-filter__swatch" style="background: <?= htmlspecialchars($clase['color']) ?>"></span>
                                <span><?= htmlspecialchars($clase['nombre']) ?></span>
                            </label>
                        <?php endforeach; ?>
                        <button type="button" class="gp-schedule-filter__clear" data-gp-class-filter-clear>Quitar filtro</button>
                    </div>
                </div>
                <p class="gp-schedule-filter__hint mb-0">Si marcas varias, ves todas las que hayas elegido.</p>
            </div>
            <?php endif; ?>

            <p class="gp-schedule-filter-empty px-4 md:px-6" data-gp-class-filter-empty hidden>Ninguna clase coincide con lo que has marcado.</p>

            <div class="table-responsive gp-schedule-table-wrap px-2 md:px-6 pb-6">
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
                                <th class="gp-schedule-hour-cell font-semibold" scope="flex flex-wrap -mx-3"><?= sprintf('%02d:00', $hora) ?></th>
                                <?php foreach ($dias as $dia): ?>
                                    <td class="gp-schedule-cell align-top">
                                        <?php
                                        foreach ($actividades as $act):
                                            $diasAct = $act['dias'] ?? [];
                                            $horaActividad = date('H', strtotime($act['fecha_inicio']));
                                            if (!in_array($dia, $diasAct, true) || (int) $horaActividad !== $hora) {
                                                continue;
                                            }
                                            $actId = (int) $act['id'];
                                            $colorAct = Actividad::normalizarColor((string) ($act['color'] ?? '')) ?? '#C45C26';
                                            $plazas = max(1, (int) ($act['plazas'] ?? 20));
                                            $fechaCelda = '';
                                            if ($scheduleWeekMonday !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $scheduleWeekMonday) && isset($offsetDiaSemana[$dia])) {
                                                $fechaCelda = (new DateTimeImmutable($scheduleWeekMonday))->modify('+' . $offsetDiaSemana[$dia] . ' days')->format('Y-m-d');
                                            }
                                            $cellKey = $fechaCelda !== '' ? ($actId . '_' . $fechaCelda) : '';
                                            $inscritos = ($cellKey !== '' && isset($inscritosPorCelda[$cellKey]))
                                                ? (int) $inscritosPorCelda[$cellKey]
                                                : 0;
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
                                                $fechaComentarios = $fechaCelda;
                                                if ($fechaComentarios === '' && (int) ($act['recurrente'] ?? 1) === 0 && !empty($act['fecha_inicio'])) {
                                                    $fechaComentarios = substr((string) $act['fecha_inicio'], 0, 10);
                                                }
                                                $fechaSesionReserva = $fechaCelda !== '' ? $fechaCelda : $fechaComentarios;
                                                $cellKeyIns = ($actId > 0 && $fechaSesionReserva !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSesionReserva))
                                                    ? ($actId . '_' . $fechaSesionReserva)
                                                    : '';
                                                $yaInscrito = $tienePlanActivo && $cellKeyIns !== '' && isset($inscritoPorSesion[$cellKeyIns]);
                                                $limiteSemanalAgotado = $tienePlanActivo && !$puedeMasReservaSemana && !$yaInscrito;
                                                $sinPlan = !$tienePlanActivo;
                                                $bloqueadoHorario = ($fechaCelda === '' && (int) ($act['recurrente'] ?? 1) === 1);
                                                $sesionReservable = true;
                                                if ($cellKey !== '') {
                                                    $sesionReservable = (bool) ($sesionReservablePorCelda[$cellKey] ?? false);
                                                } elseif ($fechaSesionReserva !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSesionReserva)) {
                                                    $sesionReservable = (bool) ($sesionReservablePorCelda[$actId . '_' . $fechaSesionReserva] ?? false);
                                                }
                                                $sesionFinalizada = $fechaSesionReserva !== ''
                                                    && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSesionReserva)
                                                    && Actividad::sesionHaFinalizado($act, $fechaSesionReserva);
                                                $urlOpiniones = '';
                                                if ($fechaComentarios !== '') {
                                                    $urlOpiniones = url('/usuario/actividades/sesion/comentarios') . '?' . http_build_query([
                                                        'actividad_id' => $actId,
                                                        'fecha' => $fechaComentarios,
                                                        'orden' => 'desc',
                                                    ]);
                                                }
                                        ?>
                                                <div class="gp-schedule-slot" data-actividad-id="<?= $actId ?>">
                                                <template id="<?= htmlspecialchars($tplId) ?>">
                                                    <div class="gp-schedule-popover-inner text-left">
                                                        <?php if ($desc !== ''): ?>
                                                            <p class="gp-schedule-pop-desc mb-2"><?= nl2br(htmlspecialchars($desc)) ?></p>
                                                        <?php endif; ?>
                                                        <ul class="gp-schedule-pop-list list-none ps-0 mb-0 text-sm">
                                                            <?php if ($fechaUnica !== ''): ?>
                                                                <li><span class="text-slate-500">Fecha</span><br><strong><?= htmlspecialchars($fechaUnica) ?></strong></li>
                                                            <?php endif; ?>
                                                            <li><span class="text-slate-500">Horario</span><br><strong><?= htmlspecialchars($horaIni) ?></strong><?= $duracion > 0 ? ' · ' . (int) $duracion . ' min' : '' ?></li>
                                                            <li><span class="text-slate-500">Monitor</span><br><strong><?= htmlspecialchars($monitorNombre !== '' ? $monitorNombre : 'Sin asignar') ?></strong></li>
                                                            <li><span class="text-slate-500">Sala</span><br><strong><?= htmlspecialchars($salaNombre !== '' ? $salaNombre : '—') ?></strong></li>
                                                            <li><span class="text-slate-500">Ocupación</span><br><strong><?= (int) $inscritos ?> / <?= (int) $plazas ?></strong> personas</li>
                                                        </ul>
                                                    </div>
                                                </template>
                                                <div class="gp-schedule-act<?= $cupoLleno ? ' gp-schedule-act--full' : '' ?><?= !$sesionReservable ? ' gp-schedule-act--past' : '' ?><?= ($yaInscrito && $sesionFinalizada) ? ' gp-schedule-act--enrolled-done' : '' ?>"
                                                     style="--gp-act-color: <?= htmlspecialchars($colorAct) ?>"
                                                     tabindex="0"
                                                     role="button"
                                                     data-gp-popover-id="<?= htmlspecialchars($tplId) ?>"
                                                     data-popover-title="<?= htmlspecialchars((string) $act['nombre']) ?>"
                                                     aria-label="Detalles de la actividad: <?= htmlspecialchars((string) $act['nombre']) ?>">
                                                    <div class="gp-schedule-act-body text-left">
                                                        <strong class="gp-schedule-act-name block text-white"><span class="gp-schedule-act-swatch" style="background: <?= htmlspecialchars($colorAct) ?>"></span><?= htmlspecialchars((string) $act['nombre']) ?></strong>
                                                        <span class="gp-schedule-act-time"><?= htmlspecialchars($horaIni) ?></span>
                                                        <span class="gp-schedule-act-capacity" title="Personas apuntadas / plazas totales"><?= (int) $inscritos ?> / <?= (int) $plazas ?></span>
                                                    </div>
                                                </div>
                                                <?php
                                                if ($yaInscrito && $sesionFinalizada): ?>
                                                    <button type="button" class="btn btn-sm w-full mb-1 gp-btn-sesion-finalizada" disabled title="Te inscribiste a esta sesión y ya ha terminado">Sesión finalizada</button>
                                                <?php elseif ($yaInscrito): ?>
                                                    <button type="button" class="btn btn-sm w-full mb-1 gp-btn-inscrito" disabled title="Ya tienes plaza en esta sesión">Inscrito</button>
                                                <?php elseif ($sinPlan): ?>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm w-full mb-1" disabled title="Contrata un plan para reservar">Requiere plan</button>
                                                <?php elseif ($limiteSemanalAgotado): ?>
                                                    <button type="button" class="btn btn-outline-warning btn-sm w-full mb-1 text-slate-800" disabled title="Has usado todas las reservas de esta semana según tu plan">Límite semanal</button>
                                                <?php elseif (!$sesionReservable): ?>
                                                    <button type="button" class="btn btn-sm w-full mb-1 gp-btn-sesion-pasada" disabled title="Esta sesión ya ha pasado">Sesión pasada</button>
                                                <?php elseif ($cupoLleno || $bloqueadoHorario): ?>
                                                    <button type="button" class="btn gp-btn-apuntarse btn-sm w-full mb-1" disabled><?= $cupoLleno ? 'Completo' : '—' ?></button>
                                                <?php else: ?>
                                                <form method="post" action="<?= htmlspecialchars(url('/usuario/inscripciones/apuntarse')) ?>" class="mb-1"
                                                      data-gp-confirm
                                                      data-gp-confirm-title="Reservar plaza"
                                                      data-gp-confirm-body="¿Confirmar tu apuntamiento a esta actividad en el horario mostrado?"
                                                      data-gp-confirm-ok="Sí, reservar">
                                                    <input type="hidden" name="actividad_id" value="<?= $actId ?>">
                                                    <input type="hidden" name="fecha_sesion" value="<?= htmlspecialchars($fechaSesionReserva) ?>">
                                                    <button type="submit" class="btn gp-btn-apuntarse btn-sm w-full"><?= $cupoLleno ? 'Completo' : 'Apuntarse' ?></button>
                                                </form>
                                                <?php endif; ?>
                                                <?php if ($urlOpiniones !== ''): ?>
                                                    <a href="<?= htmlspecialchars($urlOpiniones) ?>" class="gp-schedule-opiniones block btn btn-outline-light btn-sm w-full py-1 leading-tight text-sm no-underline">Opiniones</a>
                                                <?php endif; ?>
                                                </div>
                                        <?php
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

        <p class="text-center mt-4 mb-0">
            <a href="<?= htmlspecialchars(url('/inicio')) ?>" class="gp-schedule-backlink text-sm">← Volver al inicio</a>
        </p>
    </div>
</div>

<?php if (isset($infoMsg)): ?>
    <div class="modal fade" id="infoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content gp-modal-dark">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Información</h5>
                    <button type="button" class="btn-close btn-close-white" data-gp-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center"><?= htmlspecialchars((string) $infoMsg, ENT_QUOTES, 'UTF-8') ?></div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-light" data-gp-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new SpartumUI.Modal(document.getElementById('infoModal')).show();
        });
    </script>
<?php endif; ?>

<?php if (isset($errorMsg)): ?>
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content gp-modal-dark">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Error</h5>
                    <button type="button" class="btn-close btn-close-white" data-gp-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center"><?= htmlspecialchars((string) $errorMsg, ENT_QUOTES, 'UTF-8') ?></div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-light" data-gp-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new SpartumUI.Modal(document.getElementById('errorModal')).show();
        });
    </script>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof SpartumUI === 'undefined' || !SpartumUI.Popover) {
            return;
        }
        document.querySelectorAll('.gp-schedule-act[data-gp-popover-id]').forEach(function (el) {
            var tid = el.getAttribute('data-gp-popover-id');
            var tpl = document.getElementById(tid);
            if (!tpl) {
                return;
            }
            new SpartumUI.Popover(el, {
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
<script src="<?= htmlspecialchars(asset('js/gp-schedule-filter.js')) ?>?v=20261029a"></script>

<?php if (isset($succsessMsg)): ?>
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content gp-modal-dark">
                <div class="modal-header border-0 bg-green-600/25">
                    <h5 class="modal-title">Correcto</h5>
                    <button type="button" class="btn-close btn-close-white" data-gp-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center"><?= htmlspecialchars((string) $succsessMsg, ENT_QUOTES, 'UTF-8') ?></div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-success" data-gp-dismiss="modal">Genial</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new SpartumUI.Modal(document.getElementById('successModal')).show();
        });
    </script>
<?php endif; ?>
