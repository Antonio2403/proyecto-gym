            </div><!-- /.gp-admin-content -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div><!-- /.gp-admin-page -->

<script src="<?= htmlspecialchars(asset('js/tailwind-ui.js')) ?>"></script>
<div class="modal fade" id="gpEntityModal" tabindex="-1" aria-hidden="true" aria-labelledby="gpEntityModalTitle">
    <div class="modal-dialog modal-dialog-centered gp-entity-dialog">
        <div class="modal-content">
            <div class="modal-body gp-entity-modal__body" data-gp-entity-body>
                <h2 id="gpEntityModalTitle" class="gp-entity-modal__title">Formulario</h2>
            </div>
        </div>
    </div>
</div>
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
<script defer src="<?= htmlspecialchars(asset('js/vendor/gsap.min.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-flash-banner.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-gsap-motion.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-confirm-modal.js')) ?>?v=20261103a"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-form-unsaved.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-table-cards.js')) ?>?v=20261105a"></script>
<script defer src="<?= htmlspecialchars(asset('js/admin-datagrid.js')) ?>?v=20261108b"></script>
<script defer src="<?= htmlspecialchars(asset('js/form-validacion.js')) ?>?v=20261105c"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-entity-popup.js')) ?>?v=20261105b"></script>
<script defer
        src="<?= htmlspecialchars(asset('js/site-preferences.js')) ?>?v=20261104c"
        data-session-status-url="<?= htmlspecialchars(url('/sesion/estado')) ?>"
        data-login-url="<?= htmlspecialchars(url('/login')) ?>"
        data-cerrar-inactividad-url="<?= htmlspecialchars(url('/sesion/cerrar-inactividad')) ?>"></script>
</body>
</html>
