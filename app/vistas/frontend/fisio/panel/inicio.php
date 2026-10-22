<div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3 py-12">
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-warning"><?= htmlspecialchars((string) $_GET['error']) ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars((string) $_GET['success']) ?></div>
    <?php endif; ?>

    <div class="gp-page-header">
        <span class="gp-badge mb-2">Área profesional</span>
        <h1 class="text-[2rem] mb-0"><?= htmlspecialchars((string) ($fisio['nombre'] ?? 'Fisioterapeuta')) ?></h1>
        <?php if (!empty($fisio['especialidad'])): ?>
            <p class="text-slate-500 mb-0 mt-2"><?= htmlspecialchars((string) $fisio['especialidad']) ?></p>
        <?php endif; ?>
    </div>

    <div class="flex flex-wrap -mx-3 -mt-6">
        <div class="w-full md:w-1/2 lg:w-1/3 max-w-full shrink-0 px-3 mt-6">
            <div class="gp-stat-card h-full">
                <div class="gp-icon">📋</div>
                <h3 class="text-xl mb-2">Mis citas</h3>
                <p class="text-slate-500 text-sm mb-4">Todas las solicitudes que te llegan desde socios.</p>
                <a href="<?= htmlspecialchars(url('/fisio/citas')) ?>" class="btn btn-primary btn-sm">Ver listado</a>
            </div>
        </div>
        <div class="w-full md:w-1/2 lg:w-1/3 max-w-full shrink-0 px-3 mt-6">
            <div class="gp-stat-card h-full">
                <div class="gp-icon">✅</div>
                <h3 class="text-xl mb-2">Confirmadas</h3>
                <p class="text-slate-500 text-sm mb-4">Sesiones marcadas como confirmadas en el sistema.</p>
                <a href="<?= htmlspecialchars(url('/fisio/citas/confirmadas')) ?>" class="btn btn-primary btn-sm">Ver confirmadas</a>
            </div>
        </div>
        <div class="w-full md:w-1/2 lg:w-1/3 max-w-full shrink-0 px-3 mt-6">
            <div class="gp-stat-card h-full">
                <div class="gp-icon">⚙️</div>
                <h3 class="text-xl mb-2">Sesión</h3>
                <p class="text-slate-500 text-sm mb-4">Salir del panel cuando termines.</p>
                <a href="<?= htmlspecialchars(url('/logout')) ?>" class="btn btn-outline-light btn-sm"
                   data-gp-confirm
                   data-gp-confirm-title="Cerrar sesión"
                   data-gp-confirm-body="¿Salir del panel de fisioterapeuta?"
                   data-gp-confirm-ok="Sí, salir">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>
