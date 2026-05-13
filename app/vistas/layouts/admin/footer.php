            </div><!-- /.gp-admin-content -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div><!-- /.gp-admin-page -->

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
    <p class="small text-muted mb-3">Usamos cookies técnicas para mantener tu sesión del panel y recordar esta preferencia.</p>
    <button type="button" class="btn btn-primary btn-sm w-100" data-gp-cookie-accept>Aceptar cookies</button>
</aside>
<?php endif; ?>
<script defer src="<?= htmlspecialchars(asset('js/vendor/gsap.min.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-page-transition.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-flash-banner.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-gsap-motion.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-confirm-modal.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-form-unsaved.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/admin-datagrid.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/form-validacion.js')) ?>"></script>
<script defer
        src="<?= htmlspecialchars(asset('js/site-preferences.js')) ?>"
        data-session-status-url="<?= htmlspecialchars(url('/sesion/estado')) ?>"
        data-login-url="<?= htmlspecialchars(url('/login')) ?>"
        data-cerrar-inactividad-url="<?= htmlspecialchars(url('/sesion/cerrar-inactividad')) ?>"></script>
</body>
</html>
