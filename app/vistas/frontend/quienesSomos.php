<?php
require_once dirname(__DIR__, 3) . '/core/helpers/horario_centro.php';
$gpAboutMapa = gp_horario_centro_maps_link_url();
?>

<div class="gp-about">
    <div class="gp-about__wrap">
        <section class="gp-about-hero">
            <?= gp_responsive_picture('img/pexels/gimnasio-moderno.jpg', [
                'width' => 1600,
                'height' => 1067,
                'alt' => 'Sala de entrenamiento de Spartum, gimnasio en Lepe',
                'class' => 'gp-about-hero__photo',
                'sizes' => '(max-width: 768px) 100vw, 1120px',
                'priority' => true,
            ]) ?>
            <div class="gp-about-hero__shade" aria-hidden="true"></div>
            <div class="gp-about-hero__copy">
                <span class="gp-about-hero__badge">Sobre nosotros</span>
                <h1>Más que un gimnasio,<br><span>un equipo</span></h1>
            </div>
        </section>

        <div class="gp-about-story">
            <div class="gp-about-story__copy">
                <p>Somos un espacio pensado para quien quiere constancia, técnica y buen rollo sin perder el foco.</p>
                <p>Entrenadores titulados, salas equipadas y una comunidad que empuja en cada repetición. Aquí el objetivo no es lucir: es rendir mejor que ayer.</p>
            </div>
            <ul class="gp-about-facts">
                <li>
                    <span class="gp-about-facts__icon" aria-hidden="true"><i class="fa-solid fa-location-dot"></i></span>
                    <div>
                        <h2>Instalaciones</h2>
                        <p>Zonas de fuerza, cardio y salas para clase colectiva.</p>
                    </div>
                </li>
                <li>
                    <span class="gp-about-facts__icon" aria-hidden="true"><i class="fa-solid fa-bullseye"></i></span>
                    <div>
                        <h2>Enfoque</h2>
                        <p>Planes claros y seguimiento para cada nivel.</p>
                    </div>
                </li>
                <li>
                    <span class="gp-about-facts__icon" aria-hidden="true"><i class="fa-solid fa-fire-flame-curved"></i></span>
                    <div>
                        <h2>Energía</h2>
                        <p>Sesiones exigentes pero seguras, con monitores que corrigen y motivan.</p>
                    </div>
                </li>
            </ul>
        </div>

        <div class="gp-about-shots">
            <figure>
                <?= gp_responsive_picture('img/unsplash/zona-pesas.jpg', [
                    'width' => 1600,
                    'height' => 1067,
                    'alt' => 'Zona de pesas de Spartum',
                    'sizes' => '(max-width: 768px) 100vw, 360px',
                    'loading' => 'eager',
                ]) ?>
                <figcaption>Zona de pesas</figcaption>
            </figure>
            <figure>
                <?= gp_responsive_picture('img/unsplash/yoga-grupo.jpg', [
                    'width' => 1600,
                    'height' => 1067,
                    'alt' => 'Clase de yoga y movilidad en Spartum',
                    'sizes' => '(max-width: 768px) 100vw, 360px',
                    'loading' => 'eager',
                ]) ?>
                <figcaption>Yoga y movilidad</figcaption>
            </figure>
            <figure>
                <?= gp_responsive_picture('img/pexels/clase-colectiva.jpg', [
                    'width' => 1600,
                    'height' => 1067,
                    'alt' => 'Clase en grupo en Spartum',
                    'sizes' => '(max-width: 768px) 100vw, 360px',
                    'loading' => 'eager',
                ]) ?>
                <figcaption>Clases en grupo</figcaption>
            </figure>
        </div>

        <div class="gp-about-actions">
            <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>" class="btn btn-primary">Ver actividades</a>
            <a href="<?= htmlspecialchars(url('/contacto')) ?>" class="btn gp-about-btn">Contactar</a>
            <a href="<?= htmlspecialchars($gpAboutMapa) ?>" class="btn gp-about-btn" target="_blank" rel="noopener">Cómo llegar</a>
        </div>
    </div>
</div>
