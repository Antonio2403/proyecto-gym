<?php
$token = (string) ($token ?? '');
?>
<div class="gp-auth-shell py-5">
    <div class="container" style="max-width: 440px;">
        <div class="gp-auth-card p-4">
            <h1 class="h4 mb-3">Nueva contraseña</h1>
            <p class="text-muted small mb-4">Elige una contraseña segura para tu cuenta.</p>

            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-danger small"><?= htmlspecialchars((string) $_GET['error']) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= htmlspecialchars(url('/restablecer-contrasena')) ?>" class="needs-validation" novalidate data-gp-validate="resetPassword">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
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
                    <label class="form-label" for="clave_nueva2">Confirmar contraseña</label>
                    <input type="password" class="form-control" id="clave_nueva2" name="clave_nueva2" required minlength="16" autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3">Guardar</button>
                <p class="text-center small mb-0"><a href="<?= htmlspecialchars(url('/login')) ?>">Ir al inicio de sesión</a></p>
            </form>
        </div>
    </div>
</div>
