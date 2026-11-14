<?php
require_once dirname(__DIR__, 3) . '/core/helpers/horario_centro.php';
$gpContactoHorario = gp_horario_centro_lineas();
$gpContactoDireccion = gp_horario_centro_direccion();
$gpContactoMapa = gp_horario_centro_maps_link_url();
?>

<div class="gp-contact-page mx-auto w-full px-4 py-10 lg:py-12">
    <header class="gp-contact-head text-center">
        <span class="gp-badge">Escríbenos</span>
        <h1 class="text-[2rem] mt-4 mb-2">Hablemos</h1>
        <p class="text-slate-500 mb-0">Cuéntanos qué necesitas. El equipo de Spartum te responderá lo antes posible.</p>
    </header>

    <div class="gp-contact-layout">
        <div class="gp-contact-form-col">
            <?php if (!empty($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <div class="card gp-contact-form-card">
                <div class="card-body p-6 lg:p-8">
                    <form method="post" action="<?= htmlspecialchars(url('/contacto/enviar')) ?>" class="needs-validation" novalidate data-gp-validate="contact"
                          data-gp-confirm data-gp-confirm-title="Enviar mensaje" data-gp-confirm-body="¿Enviar este mensaje al equipo de Spartum?" data-gp-confirm-ok="Enviar">
                        <p class="gp-form-required-legend mb-4">Campos marcados con <span class="text-red-600 font-bold" aria-hidden="true">*</span> son obligatorios.</p>
                        <div class="mb-4">
                            <label class="form-label gp-label-required" for="nombre">Nombre</label>
                            <input class="form-control form-control-lg" type="text" id="nombre" name="nombre" required maxlength="150">
                        </div>
                        <div class="mb-4">
                            <label class="form-label gp-label-required" for="email">Email</label>
                            <input class="form-control form-control-lg" type="email" id="email" name="email" required maxlength="255">
                        </div>
                        <div class="mb-4">
                            <label class="form-label gp-label-required" for="asunto">Asunto</label>
                            <input class="form-control form-control-lg" type="text" id="asunto" name="asunto" required maxlength="255">
                        </div>
                        <div class="mb-6">
                            <label class="form-label gp-label-required" for="mensaje">Mensaje</label>
                            <textarea class="form-control" id="mensaje" name="mensaje" rows="6" required maxlength="8000"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-full">Enviar mensaje</button>
                    </form>
                </div>
            </div>
        </div>

        <aside class="gp-contact-visual">
            <?= gp_responsive_picture('img/pexels/entrenador-fuerza.jpg', [
                'width' => 1600,
                'height' => 1067,
                'alt' => 'Entrenadora supervisando un ejercicio de fuerza',
                'sizes' => '(max-width: 1023px) 100vw, 42vw',
            ]) ?>
            <div class="gp-contact-visual__shade" aria-hidden="true"></div>
            <div class="gp-contact-visual__content">
                <span class="gp-contact-visual__eyebrow">Ven a conocernos</span>
                <h2>Tu gimnasio en Lepe</h2>
                <p><?= htmlspecialchars($gpContactoDireccion) ?></p>
                <p class="gp-contact-visual__hours">
                    <?php foreach ($gpContactoHorario as $i => $linea): ?>
                        <?= $i > 0 ? '<br>' : '' ?><?= htmlspecialchars($linea) ?>
                    <?php endforeach; ?>
                </p>
                <a class="btn btn-light btn-sm" href="<?= htmlspecialchars($gpContactoMapa) ?>" target="_blank" rel="noopener">
                    Cómo llegar
                </a>
            </div>
        </aside>
    </div>
</div>
