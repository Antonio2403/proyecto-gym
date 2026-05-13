<?php
$tienePlan = !empty($tiene_plan);
$planActivo = is_array($plan_activo ?? null) ? $plan_activo : null;
$planNombre = trim((string) ($planActivo['plan_nombre'] ?? ''));
?>
<div class="container py-4 py-lg-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="gp-account-baja-toolbar">
                <a href="<?= htmlspecialchars(url('/inicioUsuario')) ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Volver a mi área
                </a>
            </div>

            <header class="gp-account-baja-hero mb-4" aria-labelledby="titulo-darse-de-baja">
                <div class="gp-account-baja-hero__inner">
                    <div>
                        <span class="gp-badge gp-account-baja-hero__badge">
                            <i class="fas fa-user-slash me-1" aria-hidden="true"></i> Cuenta
                        </span>
                        <h1 id="titulo-darse-de-baja">Darse de baja</h1>
                        <p class="gp-account-baja-hero__lead">
                            Solicita la baja de tu cuenta de socio al instante. Revisa las condiciones antes de confirmar:
                            perderás el acceso, deberás crear un ticket para reactivar el perfil y no conservarás tu plan activo.
                        </p>
                    </div>
                    <div class="gp-account-baja-hero__meta">
                        <span class="gp-account-baja-chip">
                            <i class="fas fa-rotate-left" aria-hidden="true"></i> Baja normal
                        </span>
                        <?php if ($tienePlan && $planNombre !== ''): ?>
                            <span class="gp-account-baja-chip gp-account-baja-chip--warn">
                                <i class="fas fa-id-card" aria-hidden="true"></i>
                                Plan activo: <?= htmlspecialchars($planNombre) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-warning border-0 shadow-sm mb-4"><?= htmlspecialchars((string) $_GET['error']) ?></div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm mb-4 gp-account-baja-card">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h6 mb-3">Condiciones de la baja</h2>
                    <ul class="gp-account-baja-terms">
                        <li>Perderás el acceso de inmediato y no podrás iniciar sesión con esta cuenta.</li>
                        <li>Tu sesión actual se cerrará en cuanto confirmes la baja.</li>
                        <li>Para volver deberás crear un <strong>ticket de reactivación</strong> y acudir a recepción, donde te facilitarán un código de 6 dígitos.</li>
                        <li>Si tienes un plan activo, se cancelará y <strong>no lo conservarás</strong> aunque reactives la cuenta o te des de alta de nuevo.</li>
                    </ul>

                    <form method="post"
                          action="<?= htmlspecialchars(url('/darse-de-baja')) ?>"
                          id="form-darse-de-baja"
                          data-gp-confirm
                          data-gp-danger="true"
                          data-gp-confirm-title="Confirmar baja de cuenta"
                          data-gp-confirm-ok="Sí, darme de baja"
                          data-gp-confirm-body="¿Confirmas que quieres darte de baja? Perderás el acceso inmediatamente, se cancelará tu plan si tienes uno y para volver necesitarás un ticket de reactivación en recepción.">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" value="1" id="acepto_terminos" name="acepto_terminos" required>
                            <label class="form-check-label" for="acepto_terminos">
                                He leído y acepto las condiciones: entiendo que no podré iniciar sesión, que deberé crear un ticket para reactivar la cuenta y que perderé mi plan activo si tengo uno.
                            </label>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-danger" id="btn-confirmar-baja" disabled>
                                Confirmar baja de cuenta
                            </button>
                            <a href="<?= htmlspecialchars(url('/inicioUsuario')) ?>" class="btn btn-outline-secondary">Cancelar y volver</a>
                        </div>
                    </form>
                </div>
            </div>

            <p class="small text-muted mb-0">
                ¿Solo quieres pausar o cambiar de plan? Consulta
                <a href="<?= htmlspecialchars(url('/pago')) ?>">suscripciones</a>
                o acude a recepción antes de darte de baja.
            </p>
        </div>
    </div>
</div>
<script>
(function () {
    var chk = document.getElementById('acepto_terminos');
    var btn = document.getElementById('btn-confirmar-baja');
    if (!chk || !btn) {
        return;
    }
    function sync() {
        btn.disabled = !chk.checked;
    }
    chk.addEventListener('change', sync);
    sync();
})();
</script>
