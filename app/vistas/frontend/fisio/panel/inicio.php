<div class="container py-5">
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-warning"><?= htmlspecialchars((string) $_GET['error']) ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars((string) $_GET['success']) ?></div>
    <?php endif; ?>

    <div class="gp-page-header">
        <span class="gp-badge mb-2">Área profesional</span>
        <h1 class="h2 mb-0"><?= htmlspecialchars((string) ($fisio['nombre'] ?? 'Fisioterapeuta')) ?></h1>
        <?php if (!empty($fisio['especialidad'])): ?>
            <p class="text-muted mb-0 mt-2"><?= htmlspecialchars((string) $fisio['especialidad']) ?></p>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <div class="gp-stat-card h-100">
                <div class="gp-icon">📋</div>
                <h3 class="h5 mb-2">Mis citas</h3>
                <p class="text-muted small mb-3">Todas las solicitudes que te llegan desde socios.</p>
                <a href="<?= htmlspecialchars(url('/fisio/citas')) ?>" class="btn btn-primary btn-sm">Ver listado</a>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="gp-stat-card h-100">
                <div class="gp-icon">✅</div>
                <h3 class="h5 mb-2">Confirmadas</h3>
                <p class="text-muted small mb-3">Sesiones marcadas como confirmadas en el sistema.</p>
                <a href="<?= htmlspecialchars(url('/fisio/citas/confirmadas')) ?>" class="btn btn-primary btn-sm">Ver confirmadas</a>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="gp-stat-card h-100">
                <div class="gp-icon">⚙️</div>
                <h3 class="h5 mb-2">Sesión</h3>
                <p class="text-muted small mb-3">Salir del panel cuando termines.</p>
                <a href="<?= htmlspecialchars(url('/logout')) ?>" class="btn btn-outline-light btn-sm"
                   data-gp-confirm
                   data-gp-confirm-title="Cerrar sesión"
                   data-gp-confirm-body="¿Salir del panel de fisioterapeuta?"
                   data-gp-confirm-ok="Sí, salir">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>
