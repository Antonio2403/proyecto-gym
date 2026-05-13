<?php
$obligatoria = !empty($obligatoria);
?>
<div class="gp-auth-shell py-5">
    <div class="container" style="max-width: 480px;">
        <div class="gp-auth-card p-4">
            <h1 class="h4 mb-3"><?= $obligatoria ? 'Actualiza tu contraseña' : 'Cambiar contraseña' ?></h1>
            <?php if ($obligatoria): ?>
                <p class="text-muted small mb-4">
                    Por seguridad debes usar una contraseña más robusta (mínimo 16 caracteres con mayúsculas, minúsculas, números y símbolos)
                    o renovarla según la política del centro. Elige una nueva contraseña antes de continuar.
                </p>
            <?php else: ?>
                <p class="text-muted small mb-4">Puedes cambiar tu contraseña cuando quieras.</p>
            <?php endif; ?>

            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-danger small"><?= htmlspecialchars((string) $_GET['error']) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= htmlspecialchars(url('/cuenta/cambiar-clave')) ?>" class="needs-validation" novalidate data-gp-validate="changePassword">
                <div class="mb-3">
                    <label class="form-label" for="clave_actual">Contraseña actual</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="clave_actual" name="clave_actual" required autocomplete="current-password">
                        <button class="btn btn-outline-secondary" type="button" data-gp-pass-reveal="clave_actual" aria-label="Mostrar u ocultar contraseña">Ver</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="clave_nueva">Nueva contraseña</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="clave_nueva" name="clave_nueva" required minlength="16" autocomplete="new-password">
                        <button class="btn btn-outline-secondary" type="button" data-gp-pass-reveal-group="clave_nueva,clave_nueva2" title="Mantén pulsado para ver ambas" aria-label="Mostrar contraseñas mientras mantienes pulsado">Ver</button>
                    </div>
                    <ul class="gp-pass-rules list-unstyled small mt-2 mb-0" data-gp-pass-rules-for="clave_nueva" aria-live="polite">
                        <li class="gp-pass-rule gp-pass-rule--idle mb-1" data-rule="len"><span class="gp-pass-rule-mark" aria-hidden="true"></span> Al menos 16 caracteres</li>
                        <li class="gp-pass-rule gp-pass-rule--idle mb-1" data-rule="upper"><span class="gp-pass-rule-mark" aria-hidden="true"></span> Una letra mayúscula</li>
                        <li class="gp-pass-rule gp-pass-rule--idle mb-1" data-rule="lower"><span class="gp-pass-rule-mark" aria-hidden="true"></span> Una letra minúscula</li>
                        <li class="gp-pass-rule gp-pass-rule--idle mb-1" data-rule="num"><span class="gp-pass-rule-mark" aria-hidden="true"></span> Un número</li>
                        <li class="gp-pass-rule gp-pass-rule--idle mb-0" data-rule="sym"><span class="gp-pass-rule-mark" aria-hidden="true"></span> Un símbolo (carácter no alfanumérico)</li>
                    </ul>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="clave_nueva2">Confirmar nueva contraseña</label>
                    <input type="password" class="form-control" id="clave_nueva2" name="clave_nueva2" required minlength="16" autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary w-100">Guardar contraseña</button>
            </form>
        </div>
    </div>
</div>
