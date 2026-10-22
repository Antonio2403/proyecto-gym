<div class="gp-schedule-page py-6 lg:py-12">
    <div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3">
        <div class="gp-schedule-panel shadow-sm">
            <div class="gp-photo-frame" style="height: 10rem; border-radius: 0; border: 0;">
                <img class="gp-photo" src="<?= htmlspecialchars(asset('img/pixabay/fisio.jpg')) ?>"
                     width="1280" height="853" alt="Tratamiento de fisioterapia en camilla">
            </div>
            <header class="gp-schedule-head text-center py-6 px-4">
                <h1 class="gp-schedule-title mb-0">Fisioterapia</h1>
                <p class="gp-schedule-subtitle mb-0 mt-2">Reserva sesiones cuando tu plan lo incluya.</p>
            </header>

            <?php if (!empty($_GET['error'])): ?>
                <div class="px-6 pb-4">
                    <div class="alert alert-warning mb-0"><?= htmlspecialchars((string) $_GET['error']) ?></div>
                </div>
            <?php endif; ?>

            <div class="px-4 md:px-6 pb-6">
                <?php if (!empty($tiene_fisio) && !empty($plan)): ?>
                    <div class="gp-light-panel-inner p-6">
                        <h2 class="text-xl mb-4">Tu plan incluye fisioterapia</h2>
                        <p class="text-slate-500 mb-2">
                            Plan: <strong class="text-slate-800"><?= htmlspecialchars($plan['plan_nombre']) ?></strong>
                        </p>
                        <?php if (!empty($plan['fecha_fin'])): ?>
                            <p class="text-slate-500 text-sm mb-6">
                                Vigencia hasta:
                                <strong><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $plan['fecha_fin']))) ?></strong>
                            </p>
                        <?php else: ?>
                            <p class="text-slate-500 text-sm mb-6">Sin fecha de fin indicada (vigente).</p>
                        <?php endif; ?>
                        <div class="flex flex-wrap gap-2">
                            <a href="<?= htmlspecialchars(url('/usuario/fisio/solicitar')) ?>" class="btn gp-btn-orange px-6">Pedir cita</a>
                            <a href="<?= htmlspecialchars(url('/usuario/fisio/mis-citas')) ?>" class="btn btn-outline-secondary">Mis citas</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="gp-light-panel-inner p-6 border-yellow-400 border-2">
                        <h2 class="text-xl mb-4">Necesitas un plan con fisioterapia</h2>
                        <p class="text-slate-500 mb-6">
                            Para solicitar citas, activa una suscripción que <strong class="text-slate-800">incluya fisioterapia</strong>
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
