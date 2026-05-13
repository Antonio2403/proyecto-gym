
<section class="gp-hero">
    <div class="container position-relative">
        <div class="row align-items-center gy-5">
            <div class="col-lg-7">
                <span class="gp-badge mb-3">Fitness · Comunidad · Resultados</span>
                <h1 class="gp-hero-title">
                    Entrena fuerte.<br>
                    <span>Vive mejor.</span>
                </h1>
                <p class="gp-hero-lead mb-4">
                    Clases guiadas, suscripciones claras y un equipo que te empuja sin perderte en el camino.
                    Tu próxima rutina empieza aquí.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'admin'): ?>
                        <a href="<?= htmlspecialchars(url('/admin')) ?>" class="btn btn-primary btn-lg px-4">Volver al panel</a>
                        <a href="<?= htmlspecialchars(url('/cuenta/perfil')) ?>" class="btn btn-outline-light btn-lg px-4">Mi perfil</a>
                    <?php elseif (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'monitor'): ?>
                        <a href="<?= htmlspecialchars(url('/inicioMonitor')) ?>" class="btn btn-primary btn-lg px-4">Volver al panel</a>
                        <a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>" class="btn btn-outline-light btn-lg px-4">Salas</a>
                    <?php elseif (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'fisio'): ?>
                        <a href="<?= htmlspecialchars(url('/fisio')) ?>" class="btn btn-primary btn-lg px-4">Panel fisio</a>
                        <a href="<?= htmlspecialchars(url('/fisio/citas')) ?>" class="btn btn-outline-light btn-lg px-4">Citas</a>
                    <?php elseif (isset($_SESSION['usuario_id'])): ?>
                        <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>" class="btn btn-primary btn-lg px-4">Ver horario</a>
                        <a href="<?= htmlspecialchars(url('/pago')) ?>" class="btn btn-outline-light btn-lg px-4">Mi plan</a>
                    <?php else: ?>
                        <a href="<?= htmlspecialchars(url('/login')) ?>" class="btn btn-primary btn-lg px-4">Crear cuenta / Entrar</a>
                        <a href="<?= htmlspecialchars(url('/quienes-somos')) ?>" class="btn btn-outline-light btn-lg px-4">Conócenos</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 overflow-hidden">
                    <div class="card-body p-4 p-lg-5">
                        <h3 class="h5 mb-3">Hoy es buen día para empezar</h3>
                        <ul class="list-unstyled text-muted small mb-4">
                            <li class="mb-2">→ Horarios organizados por salas y monitores</li>
                            <li class="mb-2">→ Pagos con Stripe, sin complicaciones</li>
                            <li class="mb-2">→ Panel para administración y monitores</li>
                        </ul>
                        <a href="<?= htmlspecialchars(url('/contacto')) ?>" class="btn btn-primary w-100">Escríbenos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="gp-section-title">Por qué Spartum</h2>
            <p class="text-muted mx-auto" style="max-width: 520px;">Menos fricción, más constancia. Diseñamos la experiencia para que solo tengas que preocuparte de dar el máximo.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="gp-stat-card">
                    <div class="gp-icon">🏋️</div>
                    <h3 class="h5 mb-2">Clases con sentido</h3>
                    <p class="text-muted small mb-0">Reserva tu plaza, revisa el monitor y mantén el ritmo semanal sin líos.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="gp-stat-card">
                    <div class="gp-icon">⚡</div>
                    <h3 class="h5 mb-2">Planes claros</h3>
                    <p class="text-muted small mb-0">Elige suscripción en segundos y renueva cuando te convenga.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="gp-stat-card">
                    <div class="gp-icon">🤝</div>
                    <h3 class="h5 mb-2">Equipo conectado</h3>
                    <p class="text-muted small mb-0">Administración y monitores coordinados para que todo fluya.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal de éxito -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title" id="successModalLabel">¡Listo!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p id="successMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Continuar</button>
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
            const modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();
        }
    });
</script>
