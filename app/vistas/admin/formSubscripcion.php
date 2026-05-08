    <div class="content-wrapper">

    <a href="<?= htmlspecialchars(url('/admin/gestionSubscripciones')) ?>" class="btn btn-secondary mb-3">Volver</a>
    <h2 class="mb-4">Crear Nueva Subscripción</h2>

    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars((string) $_GET['error']) ?></div>
    <?php endif; ?>

                <p class="gp-form-required-legend text-muted mb-3">Todos los datos son obligatorios (<span class="text-danger fw-bold" aria-hidden="true">*</span>).</p>
    <form action="<?= htmlspecialchars(url('/admin/crearSubscripcion')) ?>" method="POST" class="w-50 needs-validation" novalidate data-gp-validate="subscriptionCreate"
          data-gp-confirm data-gp-confirm-title="Crear suscripción" data-gp-confirm-body="¿Crear esta suscripción/plan?" data-gp-confirm-ok="Crear">
        <div class="mb-3">
            <label for="nombre" class="form-label gp-label-required">Nombre</label>
            <input type="text" id="nombre" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="precio" class="form-label gp-label-required">Precio (€)</label>
            <input type="number" id="precio" name="precio" step="0.01" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="duracion" class="form-label gp-label-required">Duración (meses)</label>
            <input type="number" id="duracion" name="duracion" class="form-control" required min="1" max="120" step="1">
        </div>

        <button type="submit" class="btn btn-primary">Crear Subscripción</button>
    </form>

</div>
