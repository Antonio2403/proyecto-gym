<div class="container py-5">
    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars((string) $_GET['success']) ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars((string) $_GET['error']) ?></div>
    <?php endif; ?>
    <div class="gp-page-header">
        <span class="gp-badge mb-2">Tu cuenta</span>
        <h1 class="h2 mb-0">Hola, <?= htmlspecialchars($_SESSION['nombre'] ?? 'usuario') ?></h1>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <div class="gp-stat-card h-100">
                <div class="gp-icon">📅</div>
                <h3 class="h5 mb-2">Horario</h3>
                <p class="text-muted small mb-3">Consulta actividades y apunta tu plaza.</p>
                <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>" class="btn btn-primary btn-sm">Ir al horario</a>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="gp-stat-card h-100">
                <div class="gp-icon">💳</div>
                <h3 class="h5 mb-2">Suscripción</h3>
                <p class="text-muted small mb-3">Renueva o cambia tu plan cuando quieras.</p>
                <a href="<?= htmlspecialchars(url('/pago')) ?>" class="btn btn-primary btn-sm">Ver planes</a>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="gp-stat-card h-100">
                <div class="gp-icon">🩺</div>
                <h3 class="h5 mb-2">Fisioterapia</h3>
                <p class="text-muted small mb-3">Citas incluidas en planes con fisioterapia.</p>
                <a href="<?= htmlspecialchars(url('/usuario/fisio')) ?>" class="btn btn-primary btn-sm">Ir a fisioterapia</a>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="gp-stat-card h-100">
                <div class="gp-icon">⚙️</div>
                <h3 class="h5 mb-2">Sesión</h3>
                <p class="text-muted small mb-3">Cierra sesión en este dispositivo.</p>
                <a href="<?= htmlspecialchars(url('/logout')) ?>" class="btn btn-outline-light btn-sm"
                   data-gp-confirm
                   data-gp-confirm-title="Cerrar sesión"
                   data-gp-confirm-body="¿Salir de tu cuenta en este dispositivo?"
                   data-gp-confirm-ok="Sí, salir">Salir</a>
            </div>
        </div>
    </div>
</div>
