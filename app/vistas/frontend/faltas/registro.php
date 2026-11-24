<?php
$faltas = is_array($faltas ?? null) ? $faltas : [];
?>
<div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3 py-12">
    <div class="gp-page-header">
        <span class="gp-badge mb-2">Tu cuenta</span>
        <h1 class="text-[2rem] mb-0">Tus faltas</h1>
        <p class="text-slate-500 mb-0 mt-2">Las pone el monitor de cada clase a la que te habías apuntado. Este es tu registro.</p>
    </div>

    <?php if ($faltas === []): ?>
        <div class="gp-empty">No tienes faltas de asistencia.</div>
    <?php else: ?>
        <div class="table-responsive gp-schedule-panel shadow-sm">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Clase</th>
                        <th>Hora</th>
                        <th>Monitor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($faltas as $falta): ?>
                        <?php
                        $fecha = (string) ($falta['fecha'] ?? '');
                        $fechaTxt = $fecha !== '' ? date('d/m/Y', strtotime($fecha)) : '—';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($fechaTxt) ?></td>
                            <td><?= htmlspecialchars((string) ($falta['actividad'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) (($falta['hora'] ?? '') !== '' ? $falta['hora'] : '—')) ?></td>
                            <td><?= htmlspecialchars((string) (($falta['monitor'] ?? '') !== '' ? $falta['monitor'] : '—')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <a href="<?= htmlspecialchars(url('/inicioUsuario')) ?>" class="btn btn-outline-secondary mt-6">Volver al panel</a>
</div>
