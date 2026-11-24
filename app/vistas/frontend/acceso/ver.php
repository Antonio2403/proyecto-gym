<?php
$pase = is_array($pase ?? null) ? $pase : null;
$cuando = '';
if ($pase !== null && ($pase['creado_en'] ?? '') !== '') {
    $cuando = date('d/m/Y H:i', strtotime((string) $pase['creado_en']));
}
$esEntrada = $pase !== null && ($pase['tipo'] ?? '') === 'entrada';
?>
<div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3 py-12">
    <div class="gp-stat-card" style="max-width: 32rem;">
        <?php if ($pase === null): ?>
            <h1 class="text-[2rem] mb-2">Código no válido</h1>
            <p class="text-slate-500 mb-0">Este QR no corresponde a ninguna entrada ni salida de Spartum.</p>
        <?php else: ?>
            <span class="gp-badge mb-2"><?= $esEntrada ? 'Entrada' : 'Salida' ?></span>
            <h1 class="text-[2rem] mb-2"><?= htmlspecialchars((string) ($pase['nombre'] ?? 'Socio')) ?></h1>
            <p class="text-slate-500 mb-0">
                Código de <?= $esEntrada ? 'entrada' : 'salida' ?>
                <?= $cuando !== '' ? 'generado el ' . htmlspecialchars($cuando) : '' ?>.
            </p>
        <?php endif; ?>
    </div>
</div>
