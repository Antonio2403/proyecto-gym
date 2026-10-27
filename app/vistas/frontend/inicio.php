
<section class="gp-hero gp-hero--poster">
    <div class="gp-hero-poster__stage">
        <div class="gp-hero-poster__frame">
            <img class="gp-hero-poster__photo" src="<?= htmlspecialchars(asset('img/pexels/hero-capacitacion.jpg')) ?>"
                 width="1600" height="2400" alt="Mujer entrenando con una mancuerna en un gimnasio">
        </div>
        <div class="gp-hero-poster__shade" aria-hidden="true"></div>
        <div class="gp-hero-poster__copy">
            <h1 class="gp-hero-title">
                Entrena fuerte.<br>
                <span>Vive mejor.</span>
            </h1>
            <p class="gp-hero-lead">Clases guiadas y un equipo que te empuja.</p>
            <div class="gp-hero-poster__actions">
                <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'admin'): ?>
                    <a href="<?= htmlspecialchars(url('/admin')) ?>" class="btn btn-primary btn-lg gp-btn-plain px-6">Volver al panel</a>
                    <a href="<?= htmlspecialchars(url('/cuenta/perfil')) ?>" class="btn btn-outline-light btn-lg gp-btn-plain px-6">Mi perfil</a>
                <?php elseif (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'monitor'): ?>
                    <a href="<?= htmlspecialchars(url('/inicioMonitor')) ?>" class="btn btn-primary btn-lg gp-btn-plain px-6">Volver al panel</a>
                    <a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>" class="btn btn-outline-light btn-lg gp-btn-plain px-6">Salas</a>
                <?php elseif (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'fisio'): ?>
                    <a href="<?= htmlspecialchars(url('/fisio')) ?>" class="btn btn-primary btn-lg gp-btn-plain px-6">Panel fisio</a>
                    <a href="<?= htmlspecialchars(url('/fisio/citas')) ?>" class="btn btn-outline-light btn-lg gp-btn-plain px-6">Citas</a>
                <?php elseif (isset($_SESSION['usuario_id'])): ?>
                    <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>" class="btn btn-primary btn-lg gp-btn-plain px-6">Ver horario</a>
                    <a href="<?= htmlspecialchars(url('/pago')) ?>" class="btn btn-outline-light btn-lg gp-btn-plain px-6">Mi plan</a>
                <?php else: ?>
                    <a href="<?= htmlspecialchars(url('/login')) ?>" class="btn btn-primary btn-lg gp-btn-plain px-6">Crear cuenta</a>
                    <a href="<?= htmlspecialchars(url('/quienes-somos')) ?>" class="btn btn-outline-light btn-lg gp-btn-plain px-6">Conócenos</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="gp-hero-poster__strip">
        <ul class="gp-hero-poster__points">
            <li>Horarios por sala</li>
            <li>Pagos con Stripe</li>
            <li>Equipo coordinado</li>
            <li><a href="<?= htmlspecialchars(url('/contacto')) ?>">Escríbenos</a></li>
        </ul>
    </div>
</section>

<section class="gp-editorial-photos">
    <div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3">
        <div class="gp-photo-strip">
            <figure class="gp-photo-card">
                <div class="gp-photo-frame">
                    <img class="gp-photo gp-photo--pesas" src="<?= htmlspecialchars(asset('img/unsplash/zona-pesas.jpg')) ?>"
                         width="1600" height="1067" alt="Mujer de pie entre las máquinas de la zona de pesas">
                </div>
                <figcaption>Zona de pesas</figcaption>
            </figure>
            <figure class="gp-photo-card">
                <div class="gp-photo-frame">
                    <img class="gp-photo" src="<?= htmlspecialchars(asset('img/pixabay/yoga.jpg')) ?>"
                         width="853" height="1280" alt="Sesión de yoga en una sala luminosa">
                </div>
                <figcaption>Yoga y movilidad</figcaption>
            </figure>
            <figure class="gp-photo-card">
                <div class="gp-photo-frame">
                    <img class="gp-photo" src="<?= htmlspecialchars(asset('img/pixabay/equipo.jpg')) ?>"
                         width="1280" height="853" alt="Grupo entrenando juntos en el gimnasio">
                </div>
                <figcaption>Clases en grupo</figcaption>
            </figure>
        </div>
    </div>
</section>

<section class="py-12">
    <div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3">
        <div class="text-center mb-12">
            <h2 class="gp-section-title">Por qué Spartum</h2>
            <p class="text-slate-500 mx-auto" style="max-width: 520px;">Clases guiadas, suscripciones claras y un equipo que te empuja sin perderte en el camino.</p>
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
