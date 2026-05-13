</main>
<?php
require_once dirname(__DIR__, 4) . '/core/helpers/horario_centro.php';
$gpHorarioLineas = gp_horario_centro_lineas();
$gpMapEmbed = gp_horario_centro_maps_embed_url();
$gpMapLink = gp_horario_centro_maps_link_url();
$gpDireccion = gp_horario_centro_direccion();
?>

<footer class="site-footer">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-4 col-lg-3">
                <div class="footer-brand mb-2">Spartum</div>
                <p class="text-muted small mb-0">Entrena con método. Reserva clases, gestiona tu suscripción y sigue tu progreso en un solo lugar.</p>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="footer-col-heading">Explora</div>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><a href="<?= htmlspecialchars(url('/inicio')) ?>">Inicio</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars(url('/quienes-somos')) ?>">Quiénes somos</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars(url('/contacto')) ?>">Contacto</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="footer-col-heading">Tu cuenta</div>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>">Actividades</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars(url('/usuario/inscripciones/mis-inscripciones')) ?>">Mis reservas</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars(url('/pago')) ?>">Planes</a></li>
                </ul>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="footer-col-heading">Centro</div>
                <p class="text-muted small mb-1">Horario de atención</p>
                <p class="text-muted small mb-2"><?php foreach ($gpHorarioLineas as $i => $linea): ?><?= $i > 0 ? '<br>' : '' ?><?= htmlspecialchars($linea) ?><?php endforeach; ?></p>
                <p class="text-muted small mb-2">Lepe (Huelva)</p>
            </div>
            <div class="col-12 mt-2">
                <div class="footer-col-heading mb-2">Ubicación</div>
                <p class="text-muted small mb-2">
                    Dirección exacta: <strong><?= htmlspecialchars($gpDireccion) ?></strong>.
                </p>
                <div class="gp-map-card rounded overflow-hidden border border-secondary border-opacity-25">
                    <div class="row g-0 align-items-stretch">
                        <div class="col-md-6 gp-map-embed-wrap">
                            <iframe class="gp-map-embed"
                                    src="<?= htmlspecialchars($gpMapEmbed) ?>"
                                    title="Mapa Spartum — <?= htmlspecialchars($gpDireccion) ?>"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    aria-hidden="true"
                                    tabindex="-1"></iframe>
                        </div>
                        <div class="col-md-6 p-4 d-flex flex-column justify-content-center">
                            <p class="small text-muted mb-1">Spartum · Ubicación del centro</p>
                            <p class="h6 mb-3"><?= htmlspecialchars($gpDireccion) ?></p>
                            <div>
                                <a class="btn btn-primary btn-sm"
                                   href="<?= htmlspecialchars($gpMapLink) ?>"
                                   target="_blank"
                                   rel="noopener">
                                    Abrir en Google Maps
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="border-secondary border-opacity-25 my-4">
        <p class="footer-copy mb-0 text-center text-md-start">&copy; <?= date('Y') ?> Spartum. Diseñado para rendir.</p>
    </div>
</footer>

<div class="modal fade" id="gpGlobalConfirmModal" tabindex="-1" aria-hidden="true" aria-labelledby="gpGlobalConfirmModalTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content gp-modal-confirm">
            <div class="modal-header">
                <h5 class="modal-title" id="gpGlobalConfirmModalTitle" data-gp-confirm-modal-title>Confirmar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" data-gp-confirm-modal-body>¿Continuar?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" data-gp-confirm-modal-ok>Sí</button>
            </div>
        </div>
    </div>
</div>
</div>

<?php if (($_COOKIE['gp_cookie_consent'] ?? '') !== 'accepted'): ?>
<aside class="gp-cookie-consent" data-gp-cookie-consent data-cookie-url="<?= htmlspecialchars(url('/cookies/aceptar')) ?>">
    <h2 class="h6 mb-2">Cookies de Spartum</h2>
    <p class="small text-muted mb-3">Usamos cookies técnicas para mantener tu sesión y recordar esta preferencia.</p>
    <button type="button" class="btn btn-primary btn-sm w-100" data-gp-cookie-accept>Aceptar cookies</button>
</aside>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="<?= htmlspecialchars(asset('js/vendor/gsap.min.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-page-transition.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-flash-banner.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-gsap-motion.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-confirm-modal.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-form-unsaved.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/form-validacion.js')) ?>"></script>
<script defer
        src="<?= htmlspecialchars(asset('js/site-preferences.js')) ?>"
        data-session-status-url="<?= htmlspecialchars(url('/sesion/estado')) ?>"
        data-login-url="<?= htmlspecialchars(url('/login')) ?>"
        data-cerrar-inactividad-url="<?= htmlspecialchars(url('/sesion/cerrar-inactividad')) ?>"></script>

</body>
</html>
