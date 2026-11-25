<?php $gpDentro = !empty($dentro_centro); ?>
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
            <img class="gp-user-qr__img" src="<?= htmlspecialchars(asset('img/qr-entrada.png')) ?>" width="420" height="420" alt="Código QR de entrada al centro">
            <div class="gp-user-qr__copy">
                <span class="gp-badge mb-2"><?= $gpDentro ? 'Dentro del centro' : 'Entrada' ?></span>
                <h2 class="gp-user-qr__title"><?= $gpDentro ? 'Generar salida' : 'Generar entrada' ?></h2>
                <p class="gp-user-qr__text">
                    <?= $gpDentro
                        ? 'Ya estás dentro. El siguiente código, en el mismo sitio, será de salida.'
                        : 'Genera el QR de entrada. El siguiente, en el mismo sitio, será de salida.' ?>
                </p>
                <form method="post" action="<?= htmlspecialchars(url('/usuario/acceso')) ?>" data-gp-generar-acceso>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="formato" value="json">
                    <button type="submit" class="btn btn-primary"><?= $gpDentro ? 'Generar salida' : 'Generar entrada' ?></button>
                </form>
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
</div>

<div class="gp-qr-pop" id="gpQrPop" hidden>
    <div class="gp-qr-pop__card" role="dialog" aria-modal="true" aria-labelledby="gpQrPopTitle">
        <p class="gp-badge mb-2" id="gpQrPopTitle">Entrada</p>
        <div class="gp-qr-pop__code" id="gpQrPopCode"></div>
        <p class="gp-qr-pop__hint">Acerca el lector a la pantalla. El brillo ayuda a escanear el código.</p>
        <button type="button" class="btn btn-primary" data-gp-qr-cerrar>Cerrar</button>
    </div>
</div>
<script src="<?= htmlspecialchars(asset('js/vendor/qrcode.min.js')) ?>"></script>
<script>
(function () {
    var form = document.querySelector('[data-gp-generar-acceso]');
    var pop = document.getElementById('gpQrPop');
    if (!form || !pop) return;
    var box = document.getElementById('gpQrPopCode');
    var title = document.getElementById('gpQrPopTitle');
    var busy = false;

    function cerrar() {
        window.location.reload();
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        if (busy) return;
        busy = true;
        fetch(form.getAttribute('action'), {
            method: 'POST',
            body: new FormData(form),
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            if (!data || !data.ok || !data.url || typeof QRCode === 'undefined') {
                throw new Error((data && data.error) || 'fail');
            }
            box.innerHTML = '';
            new QRCode(box, {
                text: data.url,
                width: 280,
                height: 280,
                correctLevel: QRCode.CorrectLevel.M
            });
            title.textContent = data.tipo === 'salida' ? 'Salida' : 'Entrada';
            pop.hidden = false;
            document.body.classList.add('gp-qr-pop-open');
        }).catch(function () {
            busy = false;
            window.alert('No se pudo generar el código.');
        });
    });

    pop.querySelector('[data-gp-qr-cerrar]').addEventListener('click', cerrar);
    pop.addEventListener('click', function (event) {
        if (event.target === pop) cerrar();
    });
})();
</script>
