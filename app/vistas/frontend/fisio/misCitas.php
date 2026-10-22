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
<div class="gp-schedule-page py-6 lg:py-12">
    <div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3">
        <div class="gp-schedule-panel shadow-sm">
            <header class="gp-schedule-head text-center py-6 px-4">
                <h1 class="gp-schedule-title mb-0">Mis citas</h1>
                <p class="gp-schedule-subtitle mb-0 mt-2">Fisioterapia — historial y estado.</p>
            </header>

            <div class="px-4 md:px-6 pb-4">
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

            <div class="px-4 md:px-6 pb-4">
                <div class="flex flex-wrap gap-2">
                    <?php if (!empty($tiene_fisio)): ?>
                        <a href="<?= htmlspecialchars(url('/usuario/fisio/solicitar')) ?>" class="btn gp-btn-orange btn-sm px-4">Nueva cita</a>
                    <?php endif; ?>
                    <a href="<?= htmlspecialchars(url('/usuario/fisio')) ?>" class="btn btn-outline-secondary btn-sm">Resumen</a>
                </div>
            </div>

            <div class="px-4 md:px-6 pb-6">
                <?php if (empty($tiene_fisio)): ?>
                    <div class="gp-light-panel-inner p-4 mb-4 text-sm text-slate-500">
                        No tienes ahora un plan activo con fisioterapia; puedes ver citas anteriores.
                        Para pedir una nueva, <a href="<?= htmlspecialchars(url('/pago')) ?>">renueva o cambia de plan</a>.
                    </div>
                <?php endif; ?>

                <?php if (empty($citas)): ?>
                    <div class="gp-light-panel-inner p-6 text-slate-500">No tienes citas registradas.</div>
                <?php else: ?>
                    <div class="table-responsive rounded-md overflow-hidden border border-slate-100">
                        <table class="table gp-table-light mb-0 text-center align-middle">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Profesional</th>
                                    <th class="text-left">Motivo</th>
                                    <th>Estado</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($citas as $c): ?>
                                    <tr>
                                        <td class="text-nowrap text-sm"><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $c['fecha']))) ?></td>
                                        <td class="text-sm">
                                            <?= htmlspecialchars((string) $c['fisio_nombre']) ?>
                                            <?php if (!empty($c['especialidad'])): ?>
                                                <div class="text-slate-500"><?= htmlspecialchars((string) $c['especialidad']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-left text-sm"><?= nl2br(htmlspecialchars((string) $c['motivo'])) ?></td>
                                        <td>
                                            <span class="badge rounded-full bg-slate-500">
                                                <?= htmlspecialchars($estadoEtiqueta((string) $c['estado'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (($c['estado'] ?? '') === 'S'): ?>
                                                <form method="post" action="<?= htmlspecialchars(url('/usuario/fisio/cancelar-cita')) ?>" class="inline"
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
