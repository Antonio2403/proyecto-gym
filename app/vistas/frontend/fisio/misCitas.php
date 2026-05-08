<?php
$estadoEtiqueta = static function (string $e): string {
    return match ($e) {
        'S' => 'Solicitada',
        'C' => 'Confirmada',
        'A' => 'Atendida',
        'CA' => 'Cancelada',
        default => $e,
    };
};
?>
<div class="gp-schedule-page py-4 py-lg-5">
    <div class="container">
        <div class="gp-schedule-panel shadow-sm">
            <header class="gp-schedule-head text-center py-4 px-3">
                <h1 class="gp-schedule-title mb-0">Mis citas</h1>
                <p class="gp-schedule-subtitle mb-0 mt-2">Fisioterapia — historial y estado.</p>
            </header>

            <div class="px-3 px-md-4 pb-3">
                <?php if (!empty($_GET['success'])): ?>
                    <div class="alert alert-success mb-0">Cita solicitada correctamente.</div>
                <?php endif; ?>
                <?php if (!empty($_GET['cancelada'])): ?>
                    <div class="alert alert-success mb-0">Cita cancelada.</div>
                <?php endif; ?>
                <?php if (!empty($_GET['error'])): ?>
                    <div class="alert alert-warning mb-0"><?= htmlspecialchars((string) $_GET['error']) ?></div>
                <?php endif; ?>
            </div>

            <div class="px-3 px-md-4 pb-3">
                <div class="d-flex flex-wrap gap-2">
                    <?php if (!empty($tiene_fisio)): ?>
                        <a href="<?= htmlspecialchars(url('/usuario/fisio/solicitar')) ?>" class="btn gp-btn-orange btn-sm px-3">Nueva cita</a>
                    <?php endif; ?>
                    <a href="<?= htmlspecialchars(url('/usuario/fisio')) ?>" class="btn btn-outline-secondary btn-sm">Resumen</a>
                </div>
            </div>

            <div class="px-3 px-md-4 pb-4">
                <?php if (empty($tiene_fisio)): ?>
                    <div class="gp-light-panel-inner p-3 mb-3 small text-muted">
                        No tienes ahora un plan activo con fisioterapia; puedes ver citas anteriores.
                        Para pedir una nueva, <a href="<?= htmlspecialchars(url('/pago')) ?>">renueva o cambia de plan</a>.
                    </div>
                <?php endif; ?>

                <?php if (empty($citas)): ?>
                    <div class="gp-light-panel-inner p-4 text-muted">No tienes citas registradas.</div>
                <?php else: ?>
                    <div class="table-responsive rounded-2 overflow-hidden border border-light">
                        <table class="table gp-table-light mb-0 text-center align-middle">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Profesional</th>
                                    <th class="text-start">Motivo</th>
                                    <th>Estado</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($citas as $c): ?>
                                    <tr>
                                        <td class="text-nowrap small"><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $c['fecha']))) ?></td>
                                        <td class="small">
                                            <?= htmlspecialchars((string) $c['fisio_nombre']) ?>
                                            <?php if (!empty($c['especialidad'])): ?>
                                                <div class="text-muted"><?= htmlspecialchars((string) $c['especialidad']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-start small"><?= nl2br(htmlspecialchars((string) $c['motivo'])) ?></td>
                                        <td>
                                            <span class="badge rounded-pill bg-secondary">
                                                <?= htmlspecialchars($estadoEtiqueta((string) $c['estado'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (($c['estado'] ?? '') === 'S'): ?>
                                                <form method="post" action="<?= htmlspecialchars(url('/usuario/fisio/cancelar-cita')) ?>" class="d-inline"
                                                      data-gp-confirm
                                                      data-gp-danger="true"
                                                      data-gp-confirm-title="Cancelar cita"
                                                      data-gp-confirm-body="¿Cancelar esta cita de fisioterapia?"
                                                      data-gp-confirm-ok="Sí, cancelar">
                                                    <input type="hidden" name="cita_id" value="<?= (int) $c['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Cancelar</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
