<div class="gp-schedule-page py-4 py-lg-5">
    <div class="container">
        <div class="gp-schedule-panel shadow-sm">
            <header class="gp-schedule-head text-center py-4 px-3">
                <h1 class="gp-schedule-title mb-0">Fisioterapia</h1>
                <p class="gp-schedule-subtitle mb-0 mt-2">Reserva sesiones cuando tu plan lo incluya.</p>
            </header>

            <?php if (!empty($_GET['error'])): ?>
                <div class="px-4 pb-3">
                    <div class="alert alert-warning mb-0"><?= htmlspecialchars((string) $_GET['error']) ?></div>
                </div>
            <?php endif; ?>

            <div class="px-3 px-md-4 pb-4">
                <?php if (!empty($tiene_fisio) && !empty($plan)): ?>
                    <div class="gp-light-panel-inner p-4">
                        <h2 class="h5 mb-3">Tu plan incluye fisioterapia</h2>
                        <p class="text-muted mb-2">
                            Plan: <strong class="text-dark"><?= htmlspecialchars($plan['plan_nombre']) ?></strong>
                        </p>
                        <?php if (!empty($plan['fecha_fin'])): ?>
                            <p class="text-muted small mb-4">
                                Vigencia hasta:
                                <strong><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $plan['fecha_fin']))) ?></strong>
                            </p>
                        <?php else: ?>
                            <p class="text-muted small mb-4">Sin fecha de fin indicada (vigente).</p>
                        <?php endif; ?>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= htmlspecialchars(url('/usuario/fisio/solicitar')) ?>" class="btn gp-btn-orange px-4">Pedir cita</a>
                            <a href="<?= htmlspecialchars(url('/usuario/fisio/mis-citas')) ?>" class="btn btn-outline-secondary">Mis citas</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="gp-light-panel-inner p-4 border-warning border-2">
                        <h2 class="h5 mb-3">Necesitas un plan con fisioterapia</h2>
                        <p class="text-muted mb-4">
                            Para solicitar citas, activa una suscripción que <strong class="text-dark">incluya fisioterapia</strong>
                            (en planes aparece como «Incluye fisioterapia»).
                        </p>
                        <a href="<?= htmlspecialchars(url('/pago')) ?>" class="btn gp-btn-orange">Ver planes</a>
                        <a href="<?= htmlspecialchars(url('/usuario/fisio/mis-citas')) ?>" class="btn btn-link">Ir a mis citas</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
