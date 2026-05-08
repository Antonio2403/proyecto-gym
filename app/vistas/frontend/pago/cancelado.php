<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 text-center">
            <div class="gp-result-icon gp-result-icon--muted mb-4" aria-hidden="true">✕</div>
            <span class="gp-badge mb-3">Pago no procesado</span>
            <h1 class="h2 mb-3">Has cancelado el pago</h1>
            <p class="text-muted mb-4">
                No se ha cobrado nada. Cuando quieras, puedes elegir un plan de nuevo y completar el proceso.
            </p>
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                <a href="<?= htmlspecialchars(url('/pago')) ?>" class="btn btn-primary">Volver a planes</a>
                <a href="<?= htmlspecialchars(url('/inicio')) ?>" class="btn btn-outline-light">Inicio</a>
            </div>
        </div>
    </div>
</div>
