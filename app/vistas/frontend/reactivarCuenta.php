<?php
$paso = (string) ($paso ?? '');
$solicitudId = (int) ($solicitud_id ?? 0);
?>
<div class="gp-auth-shell">
    <div class="container gp-auth-page-wrap px-3">
        <div class="gp-auth-card">
            <div class="text-center mb-4">
                <span class="gp-badge">Reactivar cuenta</span>
                <h1 class="h3 mt-3 mb-2">Vuelve a activar tu usuario</h1>
                <p class="text-muted small mb-0">Solo disponible para bajas normales. Las bajas permanentes se gestionan en recepción.</p>
            </div>

            <?php if (!empty($_GET['success'])): ?>
                <div class="alert alert-success border-0 shadow-sm"><?= htmlspecialchars((string) $_GET['success']) ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-warning border-0 shadow-sm"><?= htmlspecialchars((string) $_GET['error']) ?></div>
            <?php endif; ?>

            <?php if ($paso === 'solicitud' && $solicitudId > 0): ?>
                <div class="gp-recovery-solicitud-box text-center mb-4">
                    <div class="gp-recovery-solicitud-label mb-1">N.º de ticket de reactivación</div>
                    <div class="gp-recovery-solicitud-num">#<?= $solicitudId ?></div>
                </div>
                <div class="alert alert-info small border-0">
                    Acude a recepción con este número. Tras verificar tu identidad, te darán el código de 6 dígitos para reactivar la cuenta.
                </div>
                <a class="btn btn-primary w-100" href="<?= htmlspecialchars(url('/reactivar-cuenta') . '?paso=codigo&solicitud=' . $solicitudId) ?>">Ya tengo el código de recepción</a>
            <?php elseif ($paso === 'codigo' && $solicitudId > 0): ?>
                <form method="post" action="<?= htmlspecialchars(url('/reactivar-cuenta/codigo')) ?>" class="needs-validation" novalidate>
                    <input type="hidden" name="solicitud_id" value="<?= $solicitudId ?>">
                    <div class="mb-3">
                        <label for="codigo_verificacion" class="form-label gp-label-required">Código de 6 dígitos</label>
                        <input type="text" class="form-control form-control-lg text-center font-monospace" id="codigo_verificacion" name="codigo_verificacion" required maxlength="7" inputmode="numeric" pattern="[0-9]{3}-[0-9]{3}" placeholder="123-456" data-gp-recovery-code-input>
                        <p class="form-text small mb-0">Escribe 3 números, el guion se añade solo, y luego otros 3 números.</p>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Reactivar cuenta</button>
                </form>
            <?php else: ?>
                <form method="post" action="<?= htmlspecialchars(url('/reactivar-cuenta/solicitar')) ?>" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="dni_reactivar" class="form-label gp-label-required">DNI / NIE</label>
                        <input type="text" class="form-control form-control-lg font-monospace gp-doc-identidad-input" id="dni_reactivar" name="dni" required maxlength="9" autocomplete="off" inputmode="text" placeholder="12345678A" data-gp-doc-identidad-es>
                        <p class="form-text small mb-3">8 números y letra (DNI) o NIE (X/Y/Z + 7 números + letra). Máximo 9 caracteres.</p>
                    </div>
                    <div class="mb-3">
                        <label for="telefono_reactivar" class="form-label gp-label-required">Teléfono</label>
                        <input type="text" class="form-control form-control-lg" id="telefono_reactivar" name="telefono" required inputmode="numeric" placeholder="612 34 56 78" maxlength="12" pattern="[0-9]{3} [0-9]{2} [0-9]{2} [0-9]{2}" autocomplete="tel" data-gp-phone-input>
                    </div>
                    <div class="alert alert-info small border-0">
                        Esto crea un ticket de reactivación. El código te lo dará recepción tras comprobar tu identidad.
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Crear ticket de reactivación</button>
                </form>
            <?php endif; ?>

            <p class="text-center small mt-3 mb-0">
                <a href="<?= htmlspecialchars(url('/login')) ?>">Volver al inicio de sesión</a>
            </p>
        </div>
    </div>
</div>
<script>
(function () {
    function formatCode(raw) {
        var t = String(raw || '').replace(/[^0-9]/g, '').slice(0, 6);
        return t.length > 3 ? t.slice(0, 3) + '-' + t.slice(3) : t;
    }
    document.querySelectorAll('[data-gp-recovery-code-input]').forEach(function (el) {
        el.addEventListener('input', function () {
            el.value = formatCode(el.value);
        });
        el.addEventListener('paste', function (ev) {
            var txt = '';
            try { txt = (ev.clipboardData || window.clipboardData).getData('text') || ''; } catch (e) { return; }
            ev.preventDefault();
            el.value = formatCode(txt);
        });
    });
})();
</script>
