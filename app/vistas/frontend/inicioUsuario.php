<?php
$gpDentro = !empty($dentro_centro);
$gpFaltasTotal = (int) ($faltas_total ?? 0);
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
        <a class="gp-user-qr" href="<?= htmlspecialchars(url('/usuario/acceso')) ?>">
            <img class="gp-user-qr__img" src="<?= htmlspecialchars(asset('img/qr-entrada.png')) ?>" width="220" height="220" alt="Código QR de entrada al centro">
            <span class="gp-user-qr__copy">
                <span class="gp-badge mb-2"><?= $gpDentro ? 'Dentro del centro' : 'Entrada' ?></span>
                <span class="gp-user-qr__title"><?= $gpDentro ? 'Generar salida' : 'Generar entrada' ?></span>
                <span class="gp-user-qr__text">
                    <?= $gpDentro
                        ? 'Ya estás dentro. El siguiente código, en el mismo sitio, será de salida.'
                        : 'Genera el QR de entrada. El siguiente, en el mismo sitio, será de salida.' ?>
                </span>
                <span class="btn btn-primary"><?= $gpDentro ? 'Generar salida' : 'Generar entrada' ?></span>
            </span>
        </a>

        <nav class="gp-user-side" aria-label="Accesos de tu cuenta">
            <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>">
                <span class="gp-icon" aria-hidden="true"><i class="fa-solid fa-calendar-days"></i></span>
                <span>
                    <strong>Horario</strong>
                    <span>Consulta las clases y apunta tu plaza.</span>
                </span>
            </a>
            <a href="<?= htmlspecialchars(url('/usuario/fisio')) ?>">
                <span class="gp-icon" aria-hidden="true"><i class="fa-solid fa-stethoscope"></i></span>
                <span>
                    <strong>Fisioterapia</strong>
                    <span>Pide cita y mira las que ya tienes.</span>
                </span>
            </a>
            <a href="<?= htmlspecialchars(url('/usuario/inscripciones/mis-inscripciones')) ?>">
                <span class="gp-icon" aria-hidden="true"><i class="fa-solid fa-clipboard-check"></i></span>
                <span>
                    <strong>Mis inscripciones</strong>
                    <span>Las clases en las que ya tienes plaza.</span>
                </span>
            </a>
        </nav>
    </div>

    <div class="gp-user-more">
        <a href="<?= htmlspecialchars(url('/pago')) ?>">
            <i class="fa-solid fa-credit-card" aria-hidden="true"></i>
            Ver tu plan
        </a>
        <a href="<?= htmlspecialchars(url('/clientes/editar')) ?>">
            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
            Cambiar tus datos
        </a>
        <a href="<?= htmlspecialchars(url('/usuario/faltas')) ?>">
            <i class="fa-solid fa-user-xmark" aria-hidden="true"></i>
            <?= $gpFaltasTotal === 0 ? 'Tus faltas' : ($gpFaltasTotal === 1 ? '1 falta' : $gpFaltasTotal . ' faltas') ?>
        </a>
    </div>
</div>
