</main>
<?php
require_once dirname(__DIR__, 4) . '/core/helpers/horario_centro.php';
$gpHorarioLineas = gp_horario_centro_lineas();
$gpMapEmbed = gp_horario_centro_maps_embed_url();
$gpMapLink = gp_horario_centro_maps_link_url();
$gpDireccion = gp_horario_centro_direccion();
?>

<footer class="site-footer">
    <div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3">
        <div class="flex flex-wrap -mx-3 -mt-6">
            <div class="w-full md:w-1/3 lg:w-1/4 max-w-full shrink-0 px-3 mt-6">
                <div class="footer-brand mb-2">
                    <img src="<?= htmlspecialchars(asset('img/logo-spartum.png')) ?>?v=20261112a" alt="Spartum Gym" width="220" height="80">
                </div>
                <p class="text-white/60 text-sm mb-2">Entrena con método. Reserva clases, gestiona tu suscripción y sigue tu progreso en un solo lugar.</p>
                <p class="text-white/40 text-xs mb-0">Fotografías de <a href="https://pixabay.com/" rel="noopener noreferrer">Pixabay</a>, <a href="https://www.pexels.com/" rel="noopener noreferrer">Pexels</a> y <a href="https://unsplash.com/" rel="noopener noreferrer">Unsplash</a>.</p>
            </div>
            <div class="w-1/2 md:w-1/3 lg:w-1/4 max-w-full shrink-0 px-3 mt-6">
                <div class="footer-col-heading">Explora</div>
                <ul class="list-none ps-0 text-sm mb-0">
                    <li class="mb-2"><a href="<?= htmlspecialchars(url('/inicio')) ?>">Inicio</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars(url('/quienes-somos')) ?>">Quiénes somos</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars(url('/contacto')) ?>">Contacto</a></li>
                </ul>
            </div>
            <div class="w-1/2 md:w-1/3 lg:w-1/4 max-w-full shrink-0 px-3 mt-6">
                <div class="footer-col-heading">Tu cuenta</div>
                <ul class="list-none ps-0 text-sm mb-0">
                    <li class="mb-2"><a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>">Actividades</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars(url('/usuario/inscripciones/mis-inscripciones')) ?>">Mis reservas</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars(url('/pago')) ?>">Planes</a></li>
                </ul>
            </div>
            <div class="w-full md:w-1/3 lg:w-1/4 max-w-full shrink-0 px-3 mt-6">
                <div class="footer-col-heading">Centro</div>
                <p class="text-white/60 text-sm mb-1">Horario de atención</p>
                <p class="text-white/60 text-sm mb-2"><?php foreach ($gpHorarioLineas as $i => $linea): ?><?= $i > 0 ? '<br>' : '' ?><?= htmlspecialchars($linea) ?><?php endforeach; ?></p>
                <p class="text-white/60 text-sm mb-2">Lepe (Huelva)</p>
            </div>
            <div class="mt-2 w-full max-w-full shrink-0 px-3">
                <div class="footer-col-heading mb-2">Ubicación</div>
                <p class="text-white/60 text-sm mb-2">
                    Dirección exacta: <strong><?= htmlspecialchars($gpDireccion) ?></strong>.
                </p>
                <div class="gp-map-card rounded-lg overflow-hidden border border-slate-500/25">
                    <div class="items-stretch flex flex-wrap">
                        <div class="gp-map-embed-wrap w-full md:w-1/2 max-w-full shrink-0">
                            <iframe class="gp-map-embed"
                                    src="<?= htmlspecialchars($gpMapEmbed) ?>"
                                    title="Mapa Spartum — <?= htmlspecialchars($gpDireccion) ?>"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    aria-hidden="true"
                                    tabindex="-1"></iframe>
                        </div>
                        <div class="p-6 flex flex-col justify-center w-full md:w-1/2 max-w-full shrink-0">
                            <p class="text-sm text-white/70 mb-1">Spartum · Ubicación del centro</p>
                            <p class="text-base mb-4"><?= htmlspecialchars($gpDireccion) ?></p>
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
        <hr class="border-slate-500/25 my-6">
        <p class="footer-copy mb-0 text-center md:text-left">&copy; <?= date('Y') ?> Spartum. Diseñado para rendir.</p>
    </div>
</footer>

<div class="modal fade" id="gpGlobalConfirmModal" tabindex="-1" aria-hidden="true" aria-labelledby="gpGlobalConfirmModalTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content gp-modal-confirm">
            <div class="modal-header">
                <h5 class="modal-title" id="gpGlobalConfirmModalTitle" data-gp-confirm-modal-title>Confirmar</h5>
                <button type="button" class="btn-close" data-gp-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" data-gp-confirm-modal-body>¿Continuar?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-gp-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" data-gp-confirm-modal-ok>Sí</button>
            </div>
        </div>
    </div>
</div>
</div>

<?php require __DIR__ . '/../partials/gp_cookie_consent.php'; ?>

<script src="<?= htmlspecialchars(asset('js/tailwind-ui.js')) ?>?v=20261112a"></script>
<script defer src="<?= htmlspecialchars(asset('js/vendor/gsap.min.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-flash-banner.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-gsap-motion.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-confirm-modal.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-form-unsaved.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-table-cards.js')) ?>?v=20261105a"></script>
<script defer src="<?= htmlspecialchars(asset('js/form-validacion.js')) ?>?v=20261112e"></script>
<script defer
        src="<?= htmlspecialchars(asset('js/site-preferences.js')) ?>?v=20261104c"
        data-session-status-url="<?= htmlspecialchars(url('/sesion/estado')) ?>"
        data-login-url="<?= htmlspecialchars(url('/login')) ?>"
        data-cerrar-inactividad-url="<?= htmlspecialchars(url('/sesion/cerrar-inactividad')) ?>"></script>

</body>
</html>
