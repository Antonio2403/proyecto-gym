<?php
$ultimo = is_array($ultimo ?? null) ? $ultimo : null;
$siguiente = ($siguiente ?? 'entrada') === 'salida' ? 'salida' : 'entrada';
$dentro = $ultimo !== null && ($ultimo['tipo'] ?? '') === 'entrada';
$qrUrl = '';
if ($ultimo !== null && preg_match('/^[a-f0-9]{32}$/', (string) ($ultimo['token'] ?? ''))) {
    $qrUrl = app_url_absolute('/acceso/' . $ultimo['token']);
}
$cuando = '';
if ($ultimo !== null && ($ultimo['creado_en'] ?? '') !== '') {
    $cuando = date('d/m/Y H:i', strtotime((string) $ultimo['creado_en']));
}
?>
<div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3 py-12">
    <div class="gp-page-header">
        <span class="gp-badge mb-2">Tu cuenta</span>
        <h1 class="text-[2rem] mb-0">Entrada y salida</h1>
        <p class="text-slate-500 mb-0 mt-2">El mismo botón genera el código de entrar y el de salir. Si el último fue de entrada, el siguiente es de salida.</p>
    </div>

    <div class="gp-stat-card" style="max-width: 36rem;">
        <p class="mb-2">
            <?php if ($dentro): ?>
                <span class="badge">Dentro del centro</span>
            <?php else: ?>
                <span class="badge">Fuera del centro</span>
            <?php endif; ?>
        </p>
        <p class="text-slate-500 text-sm mb-4">
            <?php if ($dentro): ?>
                Tu último código fue de entrada<?= $cuando !== '' ? ' el ' . htmlspecialchars($cuando) : '' ?>. El siguiente será de salida.
            <?php elseif ($ultimo !== null): ?>
                Tu último código fue de salida<?= $cuando !== '' ? ' el ' . htmlspecialchars($cuando) : '' ?>. El siguiente será de entrada.
            <?php else: ?>
                Todavía no has generado ningún código. El primero será de entrada.
            <?php endif; ?>
        </p>

        <?php if ($qrUrl !== ''): ?>
            <div class="gp-qr-frame mb-3" data-gp-qr="<?= htmlspecialchars($qrUrl, ENT_QUOTES, 'UTF-8') ?>" role="img" aria-label="Código QR de <?= $ultimo['tipo'] === 'entrada' ? 'entrada' : 'salida' ?>"></div>
            <p class="font-semibold mb-1"><?= $ultimo['tipo'] === 'entrada' ? 'Entrada' : 'Salida' ?><?= $cuando !== '' ? ' · ' . htmlspecialchars($cuando) : '' ?></p>
            <p class="text-slate-500 text-sm mb-4">Enséñalo en la puerta. El centro cuenta una persona dentro por cada entrada que aún no tiene salida.</p>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars(url('/usuario/acceso')) ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-primary">
                <?= $siguiente === 'salida' ? 'Generar salida' : 'Generar entrada' ?>
            </button>
            <a href="<?= htmlspecialchars(url('/inicioUsuario')) ?>" class="btn btn-outline-secondary">Volver al panel</a>
        </form>
    </div>
</div>
<script src="<?= htmlspecialchars(asset('js/vendor/qrcode.min.js')) ?>"></script>
<script>
(function () {
    var box = document.querySelector('[data-gp-qr]');
    if (!box || typeof QRCode === 'undefined') return;
    new QRCode(box, { text: box.getAttribute('data-gp-qr'), width: 180, height: 180 });
})();
</script>
