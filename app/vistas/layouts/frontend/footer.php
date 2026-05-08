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
                <p class="text-muted small mb-0">Lun–Vie 9:00–21:00<br>Sáb 10:00–14:00</p>
            </div>
        </div>
        <hr class="border-secondary border-opacity-25 my-4">
        <p class="footer-copy mb-0 text-center text-md-start">&copy; <?= date('Y') ?> Spartum. Diseñado para rendir.</p>
    </div>
</footer>

<div class="modal fade" id="gpGlobalConfirmModal" tabindex="-1" aria-hidden="true" aria-labelledby="gpGlobalConfirmModalTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content gp-modal-dark border-secondary border-opacity-25">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title" id="gpGlobalConfirmModalTitle" data-gp-confirm-modal-title>Confirmar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-muted" data-gp-confirm-modal-body>¿Continuar?</div>
            <div class="modal-footer border-secondary border-opacity-25">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" data-gp-confirm-modal-ok>Sí</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="<?= htmlspecialchars(asset('js/gp-confirm-modal.js')) ?>"></script>
<script defer src="<?= htmlspecialchars(asset('js/form-validacion.js')) ?>"></script>

</body>
</html>
