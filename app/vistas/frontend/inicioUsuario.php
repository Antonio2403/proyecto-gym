<?php
$gpDentro = !empty($dentro_centro);
$gpFaltasTotal = (int) ($faltas_total ?? 0);
$gpFaltasTexto = $gpFaltasTotal === 0
    ? 'No tienes faltas. El monitor de tu clase las anota aquí.'
    : ($gpFaltasTotal === 1 ? 'Tienes 1 falta de asistencia.' : 'Tienes ' . $gpFaltasTotal . ' faltas de asistencia.');
?>
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

    <div class="gp-user-home">
        <div class="gp-stat-card gp-user-qr">
            <img class="gp-user-qr__img" src="<?= htmlspecialchars(asset('img/qr-entrada.png')) ?>" width="220" height="220" alt="Código QR de entrada al centro">
            <div class="gp-user-qr__copy">
                <span class="gp-badge mb-2"><?= $gpDentro ? 'Dentro del centro' : 'Entrada' ?></span>
                <h2 class="gp-user-qr__title"><?= $gpDentro ? 'Generar salida' : 'Generar entrada' ?></h2>
                <p class="gp-user-qr__text">
                    <?= $gpDentro
                        ? 'Ya estás dentro. El siguiente código, en el mismo sitio, será de salida.'
                        : 'Genera el QR de entrada. El siguiente, en el mismo sitio, será de salida.' ?>
                </p>
                <a href="<?= htmlspecialchars(url('/usuario/acceso')) ?>" class="btn btn-primary">
                    <?= $gpDentro ? 'Generar salida' : 'Generar entrada' ?>
                </a>
            </div>
        </div>

        <div class="gp-user-side">
            <div class="gp-stat-card">
                <div class="gp-photo-frame">
                    <?= gp_responsive_picture('img/pexels/clase-colectiva.jpg', [
                        'class' => 'gp-photo',
                        'width' => 1600,
                        'height' => 1068,
                        'alt' => 'Entrenador dirigiendo una clase colectiva',
                        'sizes' => '(max-width: 900px) 100vw, 24vw',
                    ]) ?>
                </div>
                <div class="gp-icon" aria-hidden="true"><i class="fa-solid fa-calendar-days"></i></div>
                <h3>Horario</h3>
                <p>Consulta actividades y apunta tu plaza.</p>
                <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>" class="btn btn-primary btn-sm">Ver tu horario</a>
            </div>
            <div class="gp-stat-card">
                <div class="gp-photo-frame">
                    <?= gp_responsive_picture('img/pixabay/fisioterapia-deportiva.jpg', [
                        'class' => 'gp-photo',
                        'width' => 1280,
                        'height' => 853,
                        'alt' => 'Profesional realizando una valoración de fisioterapia deportiva',
                        'sizes' => '(max-width: 900px) 100vw, 24vw',
                    ]) ?>
                </div>
                <div class="gp-icon" aria-hidden="true"><i class="fa-solid fa-stethoscope"></i></div>
                <h3>Fisioterapia</h3>
                <p>Citas incluidas en planes con fisioterapia.</p>
                <a href="<?= htmlspecialchars(url('/usuario/fisio')) ?>" class="btn btn-primary btn-sm">Ver tus citas</a>
            </div>
            <div class="gp-stat-card">
                <div class="gp-photo-frame">
                    <?= gp_responsive_picture('img/pexels/entrenador-fuerza.jpg', [
                        'class' => 'gp-photo',
                        'width' => 1600,
                        'height' => 1067,
                        'alt' => 'Entrenadora supervisando un ejercicio de fuerza',
                        'sizes' => '(max-width: 900px) 100vw, 24vw',
                    ]) ?>
                </div>
                <div class="gp-icon" aria-hidden="true"><i class="fa-solid fa-clipboard-check"></i></div>
                <h3>Mis inscripciones</h3>
                <p>Las clases en las que ya tienes plaza.</p>
                <a href="<?= htmlspecialchars(url('/usuario/inscripciones/mis-inscripciones')) ?>" class="btn btn-primary btn-sm">Ver tus reservas</a>
            </div>
        </div>
    </div>

    <div class="gp-user-more">
        <div class="gp-stat-card">
            <div class="gp-photo-frame">
                <?= gp_responsive_picture('img/unsplash/zona-pesas.jpg', [
                    'class' => 'gp-photo gp-photo--pesas',
                    'width' => 1600,
                    'height' => 1067,
                    'alt' => 'Zona de entrenamiento con mancuernas',
                    'sizes' => '(max-width: 767px) 100vw, 33vw',
                ]) ?>
            </div>
            <div class="gp-icon" aria-hidden="true"><i class="fa-solid fa-credit-card"></i></div>
            <h3>Suscripción</h3>
            <p>Renueva o cambia tu plan cuando quieras.</p>
            <a href="<?= htmlspecialchars(url('/pago')) ?>" class="btn btn-primary btn-sm">Ver tu plan</a>
        </div>
        <div class="gp-stat-card">
            <div class="gp-photo-frame">
                <?= gp_responsive_picture('img/pexels/gimnasio-moderno.jpg', [
                    'class' => 'gp-photo',
                    'width' => 1600,
                    'height' => 1067,
                    'alt' => 'Interior del gimnasio',
                    'sizes' => '(max-width: 767px) 100vw, 33vw',
                ]) ?>
            </div>
            <div class="gp-icon" aria-hidden="true"><i class="fa-solid fa-pen-to-square"></i></div>
            <h3>Tus datos</h3>
            <p>Nombre, correo, teléfono y contraseña.</p>
            <a href="<?= htmlspecialchars(url('/clientes/editar')) ?>" class="btn btn-primary btn-sm">Cambiar tus datos</a>
        </div>
        <div class="gp-stat-card">
            <div class="gp-photo-frame">
                <?= gp_responsive_picture('img/pexels/entrenamiento-personal.jpg', [
                    'class' => 'gp-photo',
                    'width' => 1600,
                    'height' => 1067,
                    'alt' => 'Entrenador con un socio en una sesión',
                    'sizes' => '(max-width: 767px) 100vw, 33vw',
                ]) ?>
            </div>
            <div class="gp-icon" aria-hidden="true"><i class="fa-solid fa-user-xmark"></i></div>
            <h3>Faltas de asistencia</h3>
            <p><?= htmlspecialchars($gpFaltasTexto) ?></p>
            <a href="<?= htmlspecialchars(url('/usuario/faltas')) ?>" class="btn btn-primary btn-sm">Ver tu registro</a>
        </div>
    </div>
</div>
