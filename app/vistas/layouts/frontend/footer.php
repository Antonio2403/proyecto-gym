</main>

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
                <p class="text-muted small mb-2">Lun–Vie 9:00–21:00<br>Sáb 10:00–14:00</p>
                <p class="text-muted small mb-2">Lepe (Huelva)</p>
            </div>
            <div class="col-12 mt-2">
                <div class="footer-col-heading mb-2">Ubicación</div>
                <p class="text-muted small mb-2">
                    Dirección exacta: <strong>Calle Camelia Nº 16, Lepe (Huelva)</strong>.
                </p>
                <div class="gp-map-card rounded overflow-hidden border border-secondary border-opacity-25 p-4">
                    <div class="row align-items-center g-3">
                        <div class="col-md">
                            <p class="small text-muted mb-1">Spartum · Ubicación del centro</p>
                            <p class="h6 mb-0">Calle Camelia Nº 16, Lepe (Huelva)</p>
                        </div>
                        <div class="col-md-auto">
                            <a class="btn btn-primary btn-sm"
                               href="https://www.google.com/maps/search/?api=1&amp;query=Calle%20Camelia%20N%C2%BA%2016%2C%20Lepe%2C%20Huelva"
                               target="_blank"
                               rel="noopener">
                                Abrir en Google Maps
                            </a>
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
