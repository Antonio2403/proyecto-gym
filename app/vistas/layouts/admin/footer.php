            </div><!-- /.gp-admin-content -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div><!-- /.gp-admin-page -->

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="modal fade" id="gpGlobalConfirmModal" tabindex="-1" aria-hidden="true" aria-labelledby="gpGlobalConfirmModalTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
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
<script defer src="<?= htmlspecialchars(asset('js/gp-confirm-modal.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/admin-datagrid.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/form-validacion.js')) ?>"></script>
</body>
</html>
