<div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3 py-12">
    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars((string) $_GET['success']) ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars((string) $_GET['error']) ?></div>
    <?php endif; ?>
    <div class="gp-page-header">
        <span class="gp-badge mb-2">Tu cuenta</span>
        <h1 class="text-[2rem] mb-0">Hola, <?= htmlspecialchars($_SESSION['nombre'] ?? 'usuario') ?></h1>
    </div>

    <div class="flex flex-wrap -mx-3 -mt-6">
        <div class="w-full md:w-1/2 max-w-full shrink-0 px-3 mt-6">
            <div class="gp-stat-card h-full">
                <div class="gp-photo-frame mb-4" style="height: 9rem;">
                    <img class="gp-photo" src="<?= htmlspecialchars(asset('img/pexels/clases-grupo.jpg')) ?>"
                         width="1600" height="1066" alt="Clase en grupo en Spartum, gimnasio en Lepe"
                         style="object-position: center 30%;">
                </div>
                <div class="gp-icon">📅</div>
                <h3 class="text-xl mb-2">Horario</h3>
                <p class="text-slate-500 text-sm mb-4">Consulta actividades y apunta tu plaza.</p>
                <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>" class="btn btn-primary btn-sm">Ver tu horario</a>
            </div>
        </div>
        <div class="w-full md:w-1/2 max-w-full shrink-0 px-3 mt-6">
            <div class="gp-stat-card h-full">
                <div class="gp-photo-frame mb-4" style="height: 9rem;">
                    <img class="gp-photo" src="<?= htmlspecialchars(asset('img/pixabay/mancuernas.jpg')) ?>"
                         width="1280" height="853" alt="Rack de mancuernas en Spartum, gimnasio en Lepe">
                </div>
                <div class="gp-icon">💳</div>
                <h3 class="text-xl mb-2">Suscripción</h3>
                <p class="text-slate-500 text-sm mb-4">Renueva o cambia tu plan cuando quieras.</p>
                <a href="<?= htmlspecialchars(url('/pago')) ?>" class="btn btn-primary btn-sm">Ver tu plan</a>
            </div>
        </div>
        <div class="w-full md:w-1/2 max-w-full shrink-0 px-3 mt-6">
            <div class="gp-stat-card h-full">
                <div class="gp-photo-frame mb-4" style="height: 9rem;">
                    <img class="gp-photo" src="<?= htmlspecialchars(asset('img/pixabay/yoga.jpg')) ?>"
                         width="1280" height="853" alt="Entrenamiento en Spartum, gimnasio en Lepe"
                         style="object-position: center 35%;">
                </div>
                <div class="gp-icon">✏️</div>
                <h3 class="text-xl mb-2">Tus datos</h3>
                <p class="text-slate-500 text-sm mb-4">Nombre, correo, teléfono y contraseña.</p>
                <a href="<?= htmlspecialchars(url('/clientes/editar')) ?>" class="btn btn-primary btn-sm">Cambiar tus datos</a>
            </div>
        </div>
        <div class="w-full md:w-1/2 max-w-full shrink-0 px-3 mt-6">
            <div class="gp-stat-card h-full">
                <div class="gp-photo-frame mb-4" style="height: 9rem;">
                    <img class="gp-photo" src="<?= htmlspecialchars(asset('img/pixabay/fisio.jpg')) ?>"
                         width="1280" height="853" alt="Fisioterapia en Spartum, gimnasio en Lepe">
                </div>
                <div class="gp-icon">🩺</div>
                <h3 class="text-xl mb-2">Fisioterapia</h3>
                <p class="text-slate-500 text-sm mb-4">Citas incluidas en planes con fisioterapia.</p>
                <a href="<?= htmlspecialchars(url('/usuario/fisio')) ?>" class="btn btn-primary btn-sm">Ver tus citas</a>
            </div>
        </div>
    </div>
</div>
