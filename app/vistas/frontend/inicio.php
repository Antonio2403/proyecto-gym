
<section class="gp-hero">
    <div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3 relative">
        <div class="items-center flex flex-wrap -mx-3 -mt-12">
            <div class="w-full lg:w-7/12 max-w-full shrink-0 px-3 mt-12">
                <span class="gp-badge mb-4">Fitness · Comunidad · Resultados</span>
                <h1 class="gp-hero-title">
                    Entrena fuerte.<br>
                    <span>Vive mejor.</span>
                </h1>
                <p class="gp-hero-lead mb-6">
                    Clases guiadas, suscripciones claras y un equipo que te empuja sin perderte en el camino.
                    Tu próxima rutina empieza aquí.
                </p>
                <div class="flex flex-wrap gap-4">
                    <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'admin'): ?>
                        <a href="<?= htmlspecialchars(url('/admin')) ?>" class="btn btn-primary btn-lg px-6">Volver al panel</a>
                        <a href="<?= htmlspecialchars(url('/cuenta/perfil')) ?>" class="btn btn-outline-light btn-lg px-6">Mi perfil</a>
                    <?php elseif (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'monitor'): ?>
                        <a href="<?= htmlspecialchars(url('/inicioMonitor')) ?>" class="btn btn-primary btn-lg px-6">Volver al panel</a>
                        <a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>" class="btn btn-outline-light btn-lg px-6">Salas</a>
                    <?php elseif (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'fisio'): ?>
                        <a href="<?= htmlspecialchars(url('/fisio')) ?>" class="btn btn-primary btn-lg px-6">Panel fisio</a>
                        <a href="<?= htmlspecialchars(url('/fisio/citas')) ?>" class="btn btn-outline-light btn-lg px-6">Citas</a>
                    <?php elseif (isset($_SESSION['usuario_id'])): ?>
                        <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>" class="btn btn-primary btn-lg px-6">Ver horario</a>
                        <a href="<?= htmlspecialchars(url('/pago')) ?>" class="btn btn-outline-light btn-lg px-6">Mi plan</a>
                    <?php else: ?>
                        <a href="<?= htmlspecialchars(url('/login')) ?>" class="btn btn-primary btn-lg px-6">Crear cuenta / Entrar</a>
                        <a href="<?= htmlspecialchars(url('/quienes-somos')) ?>" class="btn btn-outline-light btn-lg px-6">Conócenos</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="w-full lg:w-5/12 max-w-full shrink-0 px-3 mt-12">
                <div class="card border-0 overflow-hidden">
                    <div class="card-body p-6 lg:p-12">
                        <h3 class="text-xl mb-4">Hoy es buen día para empezar</h3>
                        <ul class="list-none ps-0 text-slate-500 text-sm mb-6">
                            <li class="mb-2">→ Horarios organizados por salas y monitores</li>
                            <li class="mb-2">→ Pagos con Stripe, sin complicaciones</li>
                            <li class="mb-2">→ Panel para administración y monitores</li>
                        </ul>
                        <a href="<?= htmlspecialchars(url('/contacto')) ?>" class="btn btn-primary w-full">Escríbenos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-12">
    <div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3">
        <div class="text-center mb-12">
            <h2 class="gp-section-title">Por qué Spartum</h2>
            <p class="text-slate-500 mx-auto" style="max-width: 520px;">Menos fricción, más constancia. Diseñamos la experiencia para que solo tengas que preocuparte de dar el máximo.</p>
        </div>
        <div class="flex flex-wrap -mx-3 -mt-6">
            <div class="w-full md:w-1/3 max-w-full shrink-0 px-3 mt-6">
                <div class="gp-stat-card">
                    <div class="gp-icon">🏋️</div>
                    <h3 class="text-xl mb-2">Clases con sentido</h3>
                    <p class="text-slate-500 text-sm mb-0">Reserva tu plaza, revisa el monitor y mantén el ritmo semanal sin líos.</p>
                </div>
            </div>
            <div class="w-full md:w-1/3 max-w-full shrink-0 px-3 mt-6">
                <div class="gp-stat-card">
                    <div class="gp-icon">⚡</div>
                    <h3 class="text-xl mb-2">Planes claros</h3>
                    <p class="text-slate-500 text-sm mb-0">Elige suscripción en segundos y renueva cuando te convenga.</p>
                </div>
            </div>
            <div class="w-full md:w-1/3 max-w-full shrink-0 px-3 mt-6">
                <div class="gp-stat-card">
                    <div class="gp-icon">🤝</div>
                    <h3 class="text-xl mb-2">Equipo conectado</h3>
                    <p class="text-slate-500 text-sm mb-0">Administración y monitores coordinados para que todo fluya.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal de éxito -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-green-600 text-white border-0">
                <h5 class="modal-title" id="successModalLabel">¡Listo!</h5>
                <button type="button" class="btn-close btn-close-white" data-gp-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p id="successMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-success" data-gp-dismiss="modal">Continuar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('success');

        if (success) {
            document.getElementById('successMessage').textContent = decodeURIComponent(success);
            const modal = new SpartumUI.Modal(document.getElementById('successModal'));
            modal.show();
        }
    });
</script>
