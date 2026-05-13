<?php
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . url('/login') . '?error=' . rawurlencode('Debes iniciar sesión para ver tus actividades'));
    exit;
}
$tienePlanActivo = !empty($tiene_plan_activo);
?>
<div class="container py-4 py-lg-5">
    <header class="gp-page-header">
        <span class="gp-badge mb-2">Reservas</span>
        <h1 class="h2 mb-2">Mis actividades</h1>
        <?php if ($tienePlanActivo): ?>
            <p class="text-muted mb-0">Sesiones en las que estás inscrito. Puedes cancelar con un clic.</p>
        <?php else: ?>
            <p class="text-muted mb-0">
                Sin <strong>plan activo</strong> no puedes cancelar ni nuevas reservas desde aquí.
                Tus inscripciones anteriores se muestran solo como referencia hasta que renueves en
                <a href="<?= htmlspecialchars(url('/pago')) ?>">Planes</a>.
            </p>
        <?php endif; ?>
    </header>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>" class="btn btn-outline-light btn-sm align-self-center">← Volver al horario</a>
        <a href="<?= htmlspecialchars(url('/inicio')) ?>" class="btn btn-outline-light btn-sm align-self-center">Inicio</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success border-0 rounded-3 mb-4 text-center">
            Inscripción cancelada correctamente
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <?php
        $errRaw = (string) ($_GET['error'] ?? '');
        $errTxt = $errRaw === '1'
            ? 'No se pudo cancelar la inscripción o no existe.'
            : htmlspecialchars($errRaw, ENT_QUOTES, 'UTF-8');
        ?>
        <div class="alert alert-danger border-0 rounded-3 mb-4 text-center">
            <?= $errTxt ?>
        </div>
    <?php endif; ?>

    <div class="card border-0">
        <div class="card-body p-0 p-md-3">
            <div class="table-responsive rounded-3 overflow-hidden">
                <table class="table table-bordered table-gym text-center align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Actividad</th>
                            <th>Día</th>
                            <th>Hora</th>
                            <th>Sesión (fecha)</th>
                            <th>Sala</th>
                            <th>Monitor</th>
                            <th>Opiniones</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($inscripciones)): ?>
                            <?php foreach ($inscripciones as $ins): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($ins['actividad']) ?></td>
                                    <td>
                                        <?php
                                        $dias = [
                                            'L' => 'Lunes',
                                            'M' => 'Martes',
                                            'X' => 'Miércoles',
                                            'J' => 'Jueves',
                                            'V' => 'Viernes',
                                            'S' => 'Sábado',
                                            'D' => 'Domingo',
                                        ];
                                        echo htmlspecialchars($dias[$ins['dia_semana']] ?? $ins['dia_semana']);
                                        ?>
                                    </td>
                                    <td class="text-nowrap small">
                                        <?= date('H:i', strtotime($ins['fecha_inicio'])) ?>
                                        –
                                        <?= date('H:i', strtotime($ins['fecha_fin'])) ?>
                                    </td>
                                    <td class="small">
                                        <?php
                                        $fecIns = isset($ins['fecha_ocurrencia']) ? trim((string) $ins['fecha_ocurrencia']) : '';
                                        if ($fecIns !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecIns)) {
                                            echo htmlspecialchars(date('d/m/Y', strtotime($fecIns)));
                                        } elseif ($fecIns !== '') {
                                            echo htmlspecialchars($fecIns);
                                        } else {
                                            echo '<span class="text-muted">—</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="small"><?= htmlspecialchars((string) ($ins['sala'] ?? '—')) ?></td>
                                    <td class="small"><?= htmlspecialchars((string) ($ins['monitor'] ?? '—')) ?></td>
                                    <td class="small">
                                        <?php
                                        $fecIns = isset($ins['fecha_ocurrencia']) ? trim((string) $ins['fecha_ocurrencia']) : '';
                                        if ($fecIns !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecIns)) {
                                            $commUrl = url('/usuario/actividades/sesion/comentarios') . '?' . http_build_query([
                                                'actividad_id' => (int) $ins['actividad_id'],
                                                'fecha' => $fecIns,
                                                'orden' => 'desc',
                                            ]);
                                            echo '<a href="' . htmlspecialchars($commUrl) . '">Opiniones</a>';
                                        } else {
                                            echo '<span class="text-muted">—</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <?php if ($tienePlanActivo): ?>
                                        <form method="post" action="<?= htmlspecialchars(url('/usuario/inscripciones/cancelar')) ?>" class="d-inline"
                                              data-gp-confirm
                                              data-gp-danger="true"
                                              data-gp-confirm-title="Cancelar reserva"
                                              data-gp-confirm-body="¿Cancelar esta inscripción? Perderás la plaza en esa sesión."
                                              data-gp-confirm-ok="Sí, cancelar">
                                            <input type="hidden" name="inscripcion_id" value="<?= (int) $ins['id'] ?>">
                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger btn-sm"
                                            >
                                                Cancelar
                                            </button>
                                        </form>
                                        <?php else: ?>
                                            <span class="small text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-muted py-5">
                                    No estás inscrito en ninguna actividad.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
