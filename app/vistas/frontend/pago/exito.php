<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 text-center">
            <div class="gp-result-icon gp-result-icon--success mb-4" aria-hidden="true">✓</div>
            <span class="gp-badge mb-3">Pago completado</span>
            <h1 class="h2 mb-3">¡Todo listo!</h1>
            <p class="text-muted mb-4">
                Tu suscripción se ha activado correctamente. Ya puedes reservar actividades y disfrutar del gimnasio.
            </p>
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>" class="btn btn-primary">Ver actividades</a>
                <a href="<?= htmlspecialchars(url('/inicio')) ?>" class="btn btn-outline-light">Volver al inicio</a>
            </div>
        </div>
    </div>
</div>
