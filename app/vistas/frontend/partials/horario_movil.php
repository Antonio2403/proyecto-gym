<?php
$gpDiasAgenda = ['L' => 'lu', 'M' => 'ma', 'X' => 'mi', 'J' => 'ju', 'V' => 'vi', 'S' => 'sá'];
$gpAgenda = [];
$gpHoyLetra = '';
$gpHoyYmd = (new DateTimeImmutable('today'))->format('Y-m-d');

foreach ($gpDiasAgenda as $gpDiaLetra => $gpDiaCorto) {
    $gpFechaDia = '';
    $gpNumDia = '';
    if ($scheduleWeekMonday !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $scheduleWeekMonday)) {
        $gpFechaObj = (new DateTimeImmutable($scheduleWeekMonday))->modify('+' . $offsetDiaSemana[$gpDiaLetra] . ' days');
        $gpFechaDia = $gpFechaObj->format('Y-m-d');
        $gpNumDia = $gpFechaObj->format('j');
        if ($gpFechaDia === $gpHoyYmd) {
            $gpHoyLetra = $gpDiaLetra;
        }
    }
    $gpItems = [];
    foreach ($actividades as $act) {
        $diasAct = $act['dias'] ?? [];
        if (!in_array($gpDiaLetra, $diasAct, true)) {
            continue;
        }
        $actId = (int) $act['id'];
        $colorAct = Actividad::normalizarColor((string) ($act['sala_color'] ?? '')) ?? '#C45C26';
        $plazas = max(1, (int) ($act['plazas'] ?? 20));
        $fechaCelda = $gpFechaDia;
        $cellKey = $fechaCelda !== '' ? ($actId . '_' . $fechaCelda) : '';
        $inscritos = ($cellKey !== '' && isset($inscritosPorCelda[$cellKey])) ? (int) $inscritosPorCelda[$cellKey] : 0;
        $monitorNombre = trim((string) ($act['monitor_nombre'] ?? ''));
        $salaNombre = trim((string) ($act['sala_nombre'] ?? ''));
        $duracion = (int) ($act['duracion'] ?? 0);
        $horaIni = date('H:i', strtotime($act['fecha_inicio']));
        $cupoLleno = $inscritos >= $plazas;
        $fechaComentarios = $fechaCelda;
        if ($fechaComentarios === '' && (int) ($act['recurrente'] ?? 1) === 0 && !empty($act['fecha_inicio'])) {
            $fechaComentarios = substr((string) $act['fecha_inicio'], 0, 10);
        }
        $fechaSesionReserva = $fechaCelda !== '' ? $fechaCelda : $fechaComentarios;
        $cellKeyIns = ($actId > 0 && $fechaSesionReserva !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSesionReserva))
            ? ($actId . '_' . $fechaSesionReserva)
            : '';
        $estuvoEnSesion = $cellKeyIns !== '' && isset($inscritoPorSesion[$cellKeyIns]);
        $yaInscrito = $tienePlanActivo && $estuvoEnSesion;
        $limiteSemanalAgotado = $tienePlanActivo && !$puedeMasReservaSemana && !$yaInscrito;
        $sinPlan = !$tienePlanActivo;
        $bloqueadoHorario = ($fechaCelda === '' && (int) ($act['recurrente'] ?? 1) === 1);
        $sesionReservable = true;
        if ($cellKey !== '') {
            $sesionReservable = (bool) ($sesionReservablePorCelda[$cellKey] ?? false);
        }
        $sesionCancelada = $cellKey !== '' && isset($sesionesCanceladas[$cellKey]);
        $sesionFinalizada = $fechaSesionReserva !== ''
            && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSesionReserva)
            && Actividad::sesionHaFinalizado($act, $fechaSesionReserva);
        $inicioTs = $fechaSesionReserva !== '' ? strtotime($fechaSesionReserva . ' ' . $horaIni) : false;
        $finTs = $inicioTs ? $inicioTs + max(1, $duracion) * 60 : false;
        $enCurso = $inicioTs && $finTs && time() >= $inicioTs && time() < $finTs && !$sesionCancelada;
        $urlOpiniones = '';
        if ($fechaComentarios !== '') {
            $urlOpiniones = url('/usuario/actividades/sesion/comentarios') . '?' . http_build_query([
                'actividad_id' => $actId,
                'fecha' => $fechaComentarios,
                'orden' => 'desc',
            ]);
        }
        if ($sesionCancelada) {
            $estado = 'Cancelada';
            $estadoClase = 'is-muted';
        } elseif ($estuvoEnSesion && $sesionFinalizada) {
            $estado = 'Completado';
            $estadoClase = 'is-muted';
        } elseif ($enCurso) {
            $estado = 'En curso';
            $estadoClase = 'is-live';
        } elseif ($sesionFinalizada || !$sesionReservable) {
            $estado = 'Finalizada';
            $estadoClase = 'is-muted';
        } else {
            $estado = (int) $inscritos . '/' . (int) $plazas . ' participantes';
            $estadoClase = '';
        }
        $gpItems[] = compact(
            'act', 'actId', 'colorAct', 'plazas', 'inscritos', 'monitorNombre', 'salaNombre', 'duracion',
            'horaIni', 'cupoLleno', 'estuvoEnSesion', 'yaInscrito', 'limiteSemanalAgotado', 'sinPlan',
            'bloqueadoHorario', 'sesionReservable', 'sesionCancelada', 'sesionFinalizada', 'urlOpiniones',
            'estado', 'estadoClase', 'fechaSesionReserva'
        );
    }
    usort($gpItems, static function (array $a, array $b): int {
        return [$a['horaIni'], $a['act']['nombre']] <=> [$b['horaIni'], $b['act']['nombre']];
    });
    $gpAgenda[] = [
        'letra' => $gpDiaLetra,
        'corto' => $gpDiaCorto,
        'num' => $gpNumDia,
        'hoy' => $gpDiaLetra === $gpHoyLetra,
        'items' => $gpItems,
    ];
}
$gpDiaInicial = $gpHoyLetra !== '' ? $gpHoyLetra : 'L';
?>
<div class="gp-day-agenda" data-gp-day-agenda>
    <div class="gp-day-agenda__days" role="tablist" aria-label="Día de la semana">
        <?php foreach ($gpAgenda as $gpDia): ?>
            <button type="button"
                    class="gp-day-agenda__day<?= $gpDia['letra'] === $gpDiaInicial ? ' is-selected' : '' ?><?= $gpDia['hoy'] ? ' is-today' : '' ?>"
                    data-gp-day="<?= htmlspecialchars($gpDia['letra']) ?>"
                    role="tab"
                    aria-selected="<?= $gpDia['letra'] === $gpDiaInicial ? 'true' : 'false' ?>">
                <span class="gp-day-agenda__dow"><?= htmlspecialchars($gpDia['corto']) ?></span>
                <span class="gp-day-agenda__num"><?= htmlspecialchars($gpDia['num'] !== '' ? $gpDia['num'] : '–') ?></span>
            </button>
        <?php endforeach; ?>
    </div>

    <?php foreach ($gpAgenda as $gpDia): ?>
        <div class="gp-day-panel" data-gp-day-panel="<?= htmlspecialchars($gpDia['letra']) ?>"<?= $gpDia['letra'] === $gpDiaInicial ? '' : ' hidden' ?>>
            <p class="gp-day-panel__empty">No hay clases este día.</p>
            <?php foreach ($gpDia['items'] as $s): ?>
                <article class="gp-day-row gp-schedule-slot" data-actividad-id="<?= (int) $s['actId'] ?>">
                    <span class="gp-day-row__mark" style="background: <?= htmlspecialchars($s['colorAct']) ?>" aria-hidden="true"></span>
                    <div class="gp-day-row__body">
                        <h3 class="gp-day-row__title"><?= htmlspecialchars(($s['salaNombre'] !== '' ? $s['salaNombre'] . '/' : '') . (string) $s['act']['nombre']) ?></h3>
                        <p class="gp-day-row__meta"><?= htmlspecialchars($s['horaIni']) ?><?= $s['duracion'] > 0 ? ' · ' . (int) $s['duracion'] . ' min' : '' ?></p>
                        <p class="gp-day-row__who"><?= htmlspecialchars($s['monitorNombre'] !== '' ? $s['monitorNombre'] : 'Sin monitor') ?></p>
                        <p class="gp-day-row__status <?= htmlspecialchars($s['estadoClase']) ?>"><?= htmlspecialchars($s['estado']) ?></p>
                    </div>
                    <div class="gp-day-row__actions">
                        <?php if ($s['sesionCancelada']): ?>
                            <span class="gp-day-row__pill">Cancelada</span>
                        <?php elseif ($s['estuvoEnSesion'] && $s['sesionFinalizada'] && $s['urlOpiniones'] !== ''): ?>
                            <a href="<?= htmlspecialchars($s['urlOpiniones']) ?>" class="btn btn-sm gp-btn-comentar">Comentar</a>
                        <?php elseif ($s['yaInscrito']): ?>
                            <span class="gp-day-row__pill">Inscrito</span>
                        <?php elseif ($s['limiteSemanalAgotado']): ?>
                            <button type="button" class="btn btn-sm gp-btn-apuntarse" disabled>Apuntarse</button>
                        <?php elseif (!$s['sesionReservable'] || $s['bloqueadoHorario']): ?>
                            <button type="button" class="btn btn-sm gp-btn-apuntarse" disabled>Apuntarse</button>
                        <?php elseif ($s['cupoLleno']): ?>
                            <button type="button" class="btn btn-sm gp-btn-apuntarse" disabled>Completo</button>
                        <?php else: ?>
                            <form method="post" action="<?= htmlspecialchars(url('/usuario/inscripciones/apuntarse')) ?>"
                                  data-gp-confirm
                                  data-gp-confirm-title="Reservar plaza"
                                  data-gp-confirm-body="¿Confirmar tu apuntamiento a esta actividad en el horario mostrado?"
                                  data-gp-confirm-ok="Sí, reservar">
                                <input type="hidden" name="actividad_id" value="<?= (int) $s['actId'] ?>">
                                <input type="hidden" name="fecha_sesion" value="<?= htmlspecialchars($s['fechaSesionReserva']) ?>">
                                <button type="submit" class="btn btn-sm gp-btn-apuntarse">Apuntarse</button>
                            </form>
                        <?php endif; ?>
                        <?php if (!$s['sesionCancelada'] && $s['urlOpiniones'] !== '' && !($s['estuvoEnSesion'] && $s['sesionFinalizada'])): ?>
                            <a href="<?= htmlspecialchars($s['urlOpiniones']) ?>" class="gp-day-row__opiniones">Opiniones</a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
<script>
(function () {
    var root = document.querySelector('[data-gp-day-agenda]');
    if (!root) return;
    var buttons = root.querySelectorAll('[data-gp-day]');
    var panels = root.querySelectorAll('[data-gp-day-panel]');
    function show(day) {
        Array.prototype.forEach.call(buttons, function (btn) {
            var on = btn.getAttribute('data-gp-day') === day;
            btn.classList.toggle('is-selected', on);
            btn.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        Array.prototype.forEach.call(panels, function (panel) {
            panel.hidden = panel.getAttribute('data-gp-day-panel') !== day;
        });
    }
    Array.prototype.forEach.call(buttons, function (btn) {
        btn.addEventListener('click', function () {
            show(btn.getAttribute('data-gp-day'));
        });
    });
})();
</script>
