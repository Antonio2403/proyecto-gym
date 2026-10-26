<script src="https://js.stripe.com/v3/"></script>

<?php
$planActivo = $plan_activo ?? null;
$subscripciones = $subscripciones ?? [];
$idPlanActual = isset($planActivo['plan_id']) ? (int) $planActivo['plan_id'] : null;
$esCliente = isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'cliente';
$stripePk = htmlspecialchars($_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '');
$planesComparativa = $planActivo && $idPlanActual !== null
    ? array_values(array_filter($subscripciones, static fn($s) => (int) ($s['id'] ?? 0) !== $idPlanActual))
    : $subscripciones;

$gpMarcaPlan = static function (bool $incluido): string {
    $clase = $incluido ? 'gp-plan-mark gp-plan-mark--yes' : 'gp-plan-mark gp-plan-mark--no';
    $texto = $incluido ? 'Incluida' : 'No incluida';
    $trazo = $incluido
        ? '<path d="M3.2 8.4 6.4 11.6 12.8 4.4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>'
        : '<path d="M4.2 4.2 11.8 11.8M11.8 4.2 4.2 11.8" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>';

    return '<span class="' . $clase . '" role="img" aria-label="' . $texto . '"><svg viewBox="0 0 16 16" aria-hidden="true">' . $trazo . '</svg></span>';
};

$gpFilaPlan = static function (bool $incluido, string $titulo, string $detalle = '') use ($gpMarcaPlan): string {
    $extra = $detalle !== '' ? '<small>' . htmlspecialchars($detalle, ENT_QUOTES, 'UTF-8') . '</small>' : '';

    return '<li class="' . ($incluido ? 'is-yes' : 'is-no') . '">'
        . $gpMarcaPlan($incluido)
        . '<span><strong>' . htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') . '</strong>' . $extra . '</span></li>';
};

$gpTextoMeses = static function (int $meses): string {
    if ($meses <= 0) {
        return 'Sin duración publicada';
    }

    return $meses === 1 ? '1 mes' : $meses . ' meses';
};
?>

<div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3 py-12">
    <?php if (!empty($_GET['info'])): ?>
        <div class="alert alert-info border-0 shadow-sm mb-6"><?= htmlspecialchars((string) $_GET['info']) ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-warning border-0 shadow-sm mb-6"><?= htmlspecialchars((string) $_GET['error']) ?></div>
    <?php endif; ?>

    <header class="gp-page-header text-center mb-6 lg:mb-12">
        <span class="gp-badge mb-2">Suscripciones</span>
        <h1 class="text-[2rem] mb-2">Planes Spartum</h1>
        <p class="text-slate-500 mb-0 mx-auto gp-pago-lead">
            Comparativa clara de lo que incluye cada tarifa. El pago es seguro con Stripe.
            <?php if (!$esCliente): ?>
                <span class="block mt-2 text-sm"><a href="<?= htmlspecialchars(url('/login')) ?>">Inicia sesión como cliente</a> para poder contratar un plan.</span>
            <?php endif; ?>
        </p>
    </header>

    <div class="gp-photo-frame gp-photo-frame--wide mb-8 mx-auto" style="max-width: 960px;">
        <img class="gp-photo" src="<?= htmlspecialchars(asset('img/pixabay/mancuernas.jpg')) ?>"
             width="1280" height="853" alt="Zona de mancuernas del gimnasio">
    </div>

    <?php if ($planActivo): ?>
        <div class="gp-plan-current-banner gp-plan-current-banner--hero mb-6 mx-auto">
            <div class="items-center flex flex-wrap -mx-2 -mt-4">
                <div class="w-full md:w-2/3 max-w-full shrink-0 px-2 mt-4">
                    <span class="gp-badge gp-badge-soft mb-2 inline-block">Tu plan actual</span>
                    <?php if ((int) ($planActivo['en_oferta'] ?? 0) === 1): ?>
                        <span class="gp-badge gp-badge-soft mb-2 inline-block">Oferta contratada</span>
                    <?php endif; ?>
                    <h2 class="text-xl mb-1 text-slate-800"><?= htmlspecialchars((string) ($planActivo['plan_nombre'] ?? 'Plan')) ?></h2>
                    <p class="text-slate-500 text-sm mb-0">
                        Vigente hasta:
                        <?= !empty($planActivo['fecha_fin'])
                            ? '<strong>' . htmlspecialchars(substr((string) $planActivo['fecha_fin'], 0, 16)) . '</strong>'
                            : '<strong>sin fecha de fin definida</strong>' ?>
                        · Mientras esté activo no puedes contratar otro plan diferente desde la web.
                    </p>
                    <div class="gp-current-plan-details mt-4">
                        <span><strong>Precio:</strong> <?= isset($planActivo['precio']) ? htmlspecialchars((string) $planActivo['precio']) . ' €' : 'no disponible' ?></span>
                        <span><strong>Duración contratada:</strong> <?= (int) ($planActivo['duracion'] ?? 0) ?> mes(es)</span>
                        <span><strong>Clases/semana:</strong> <?= (int) ($planActivo['numero_clases'] ?? 0) > 0 ? (int) $planActivo['numero_clases'] : 'sin límite explícito' ?></span>
                        <span class="gp-current-plan-details__mark"><strong>Fisioterapia</strong> <?= $gpMarcaPlan((($planActivo['fisio'] ?? '') === 'S')) ?></span>
                        <?php if (($planActivo['plan_catalogo_estado'] ?? 'A') !== 'A'): ?>
                            <span><strong>Catálogo:</strong> ya no disponible para nuevas compras</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="md:text-right w-full md:w-1/3 max-w-full shrink-0 px-2 mt-4">
                    <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>" class="btn btn-primary btn-sm">Ir a actividades</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="flex flex-wrap items-end justify-between gap-4 mb-4">
        <div>
            <h2 class="text-2xl mb-1">Planes disponibles</h2>
            <p class="text-slate-500 text-sm mb-0">
                <?= $planActivo
                    ? 'Puedes consultar otros planes, pero solo recepción puede cambiar o cancelar tu plan activo.'
                    : 'Revisa de un vistazo precio, duración, clases, fisioterapia y disponibilidad.' ?>
            </p>
        </div>
    </div>

    <div class="gp-plans-grid">
        <?php foreach ($planesComparativa as $sub): ?>
            <?php
            $sid = (int) ($sub['id'] ?? 0);
            $esActual = $idPlanActual !== null && $sid === $idPlanActual;
            $bloqueadoPorOtroPlan = $esCliente && $planActivo !== null && !$esActual;
            $clasesPorSemana = (int) ($sub['numero_clases'] ?? 0);
            $durMeses = (int) ($sub['duracion'] ?? 0);
            $conFisio = (($sub['fisio'] ?? '') === 'S');
            $precioNum = isset($sub['precio']) && $sub['precio'] !== '' ? (float) $sub['precio'] : null;
            $cuotaMes = ($precioNum !== null && $durMeses > 0) ? $precioNum / $durMeses : null;
            $textoClases = $clasesPorSemana > 0 ? 'Hasta ' . $clasesPorSemana . ' por semana' : 'Sin tope publicado';
            $esOferta = (int) ($sub['en_oferta'] ?? 0) === 1 && !empty($sub['oferta_fin']);
            $ofertaFin = $esOferta ? (string) $sub['oferta_fin'] : '';
            $ofertaMotivo = trim((string) ($sub['oferta_motivo'] ?? 'Oferta limitada'));
            $retiradoCatalogo = (string) ($sub['estado'] ?? 'A') !== 'A';
            $comprable = !$retiradoCatalogo && (!$esOferta || strtotime($ofertaFin) > time());
            $puedePagar = $esCliente && !$planActivo && $comprable;
            ?>
            <article class="gp-plan-card<?=
                $esActual ? ' gp-plan-card--current' : ''
                ?><?=
                ($bloqueadoPorOtroPlan || (!$esCliente && !$esActual) || (!$comprable && !$esActual)) ? ' gp-plan-card--muted' : ''
                ?>">
                <?php if ($esActual): ?>
                    <span class="gp-plan-ribbon"><span class="me-1">✓</span> Tu plan</span>
                <?php elseif ($esOferta): ?>
                    <span class="gp-plan-ribbon gp-plan-ribbon--offer">Oferta</span>
                <?php endif; ?>
                <header class="gp-plan-card-head pb-4">
                    <div class="flex justify-between items-start gap-4">
                        <div>
                            <h2 class="text-2xl mb-1"><?= htmlspecialchars((string) ($sub['nombre'] ?? 'Plan')) ?></h2>
                            <p class="text-sm text-slate-500 mb-0"><?= $esOferta ? 'Oferta por tiempo limitado' : 'Plan del catálogo' ?></p>
                        </div>
                        <div class="gp-plan-card-price text-right"><?= isset($sub['precio']) ? htmlspecialchars((string) $sub['precio']) . ' €' : '—' ?></div>
                    </div>
                    <?php if ($esOferta && !$retiradoCatalogo): ?>
                        <div class="gp-plan-offer-box mt-4" data-offer-countdown="<?= htmlspecialchars($ofertaFin) ?>">
                            <strong><?= htmlspecialchars($ofertaMotivo !== '' ? $ofertaMotivo : 'Oferta limitada') ?></strong>
                            <span class="block text-sm">Disponible para comprar hasta: <?= htmlspecialchars(substr($ofertaFin, 0, 16)) ?></span>
                            <span class="block text-sm" data-offer-countdown-label>Calculando tiempo restante...</span>
                        </div>
                    <?php endif; ?>
                </header>

                <div class="gp-plan-comparison text-sm">
                    <div><span>Duración</span><strong><?= htmlspecialchars($gpTextoMeses($durMeses)) ?></strong></div>
                    <div><span>Clases/semana</span><strong><?= $clasesPorSemana > 0 ? 'hasta ' . $clasesPorSemana : 'sin tope' ?></strong></div>
                    <div><span>Cuota media</span><strong><?= $cuotaMes !== null ? htmlspecialchars(number_format($cuotaMes, 2, ',', '.') . ' €/mes') : '—' ?></strong></div>
                </div>

                <ul class="gp-plan-features list-none ps-0 text-sm my-6 grow">
                    <?= $gpFilaPlan(true, 'Reserva online', 'Elige la actividad y guarda tu plaza') ?>
                    <?= $gpFilaPlan(true, 'Horario del centro', 'Ves sala, monitor y día antes de apuntarte') ?>
                    <?= $gpFilaPlan(true, 'Clases guiadas', $textoClases) ?>
                    <?= $gpFilaPlan($conFisio, 'Fisioterapia', $conFisio ? 'Puedes pedir cita desde tu cuenta' : '') ?>
                    <?= $gpFilaPlan(true, 'Pago con Stripe', 'Contratas el plan desde esta página') ?>
                    <?= $gpFilaPlan(true, 'Recepción', 'Altas, cambios y bajas se tramitan en el centro') ?>
                </ul>

                <?php if ($bloqueadoPorOtroPlan): ?>
                    <div class="grid gap-2">
                        <button type="button" class="btn btn-outline-secondary" disabled>Plan activo en tu cuenta</button>
                        <span class="text-center text-sm text-slate-500">Para cambiar, cancelar o darte de baja debes contactar con recepción.</span>
                    </div>
                <?php elseif (!$esCliente): ?>
                    <div class="grid gap-2 mt-auto">
                        <a href="<?= htmlspecialchars(url('/login')) ?>" class="btn btn-primary font-semibold">Inicia sesión para contratar</a>
                    </div>
                <?php elseif ($esActual): ?>
                    <button type="button" class="btn btn-success font-semibold w-full" disabled>Tu suscripción vigente</button>
                    <p class="text-sm text-slate-500 text-center mb-0 mt-2">Vence el <?= !empty($planActivo['fecha_fin']) ? htmlspecialchars(substr((string) $planActivo['fecha_fin'], 0, 16)) : 'día indicado por el centro' ?>.</p>
                <?php else: ?>
                    <button type="button" class="btn btn-dark font-semibold w-full"
                            <?= $puedePagar ? '' : 'disabled title="No disponible"' ?>
                            data-sub-id="<?= $sid ?>">
                        <?= $puedePagar ? 'Contratar este plan' : 'No disponible' ?>
                    </button>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if (empty($planesComparativa)): ?>
        <p class="alert alert-secondary text-center">No hay planes publicados por el momento.</p>
    <?php endif; ?>

    <div class="justify-center mt-12 flex flex-wrap -mx-3">
        <div class="w-full lg:w-1/2 max-w-full shrink-0 px-3">
            <div id="payment-wrap" class="gp-pay-box card border-0 shadow-lg" style="display: none;">
                <div class="card-body p-6 lg:p-12">
                    <h3 class="text-base uppercase text-slate-500 tracking-wide mb-4">Completa el pago con Stripe</h3>
                    <form id="payment-form">
                        <div id="payment-element"></div>
                        <button type="submit" id="submit" class="btn btn-success btn-lg w-full mt-4">Pagar ahora</button>
                    </form>
                </div>
            </div>
            <p id="error-message" class="text-red-600 text-center text-sm mt-4 mb-0"></p>
        </div>
    </div>
</div>

<script>
(function () {
    <?php if ($stripePk !== ''): ?>
    const stripe = Stripe(<?= json_encode($stripePk, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);
    <?php else: ?>
    const stripe = null;
    <?php endif; ?>

    window.subscripcion_id = 0;
    let elements;

    window.iniciarPago = function (subscripcion_id) {
        const errBox = document.getElementById('error-message');
        errBox.textContent = '';
        <?php if ($stripePk === ''): ?>
        errBox.textContent = 'Falta configurar la clave pública de Stripe.';
        return;
        <?php endif; ?>

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') || '' : '';
        fetch(<?= json_encode(url('/pago/crear-intento'), JSON_UNESCAPED_SLASHES) ?>, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
            body: JSON.stringify({ subscripcion_id }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (!data.clientSecret) throw new Error(data.error || 'Error al crear el pago');
                elements = stripe.elements({ clientSecret: data.clientSecret });
                const elMount = document.getElementById('payment-element');
                elMount.innerHTML = '';
                const paymentElement = elements.create('payment');
                paymentElement.mount('#payment-element');
                const wrap = document.getElementById('payment-wrap');
                wrap.style.display = 'block';
                wrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
                window.subscripcion_id = subscripcion_id;
            })
            .catch((err) => {
                errBox.textContent = err.message;
            });
    };

    document.querySelectorAll('button[data-sub-id]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = parseInt(btn.getAttribute('data-sub-id'), 10);
            if (id > 0) iniciarPago(id);
        });
    });

    const pf = document.getElementById('payment-form');
    if (pf && stripe) {
        pf.addEventListener('submit', async function (e) {
            e.preventDefault();
            const errBox = document.getElementById('error-message');
            let sid = window.subscripcion_id;
            if (sid == null || Number(sid) <= 0) {
                errBox.textContent = 'Primero elige un plan.';
                return;
            }
            if (typeof elements === 'undefined' || !elements) {
                errBox.textContent = 'Pulsa «Contratar este plan» en la tarifa elegida.';
                return;
            }
            const submitBtn = pf.querySelector('button[type=\"submit\"]');
            if (submitBtn) submitBtn.disabled = true;
            try {
                const { error } = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: window.location.origin + <?= json_encode(url('/pago/exito'), JSON_UNESCAPED_SLASHES) ?>,
                    },
                });
                if (error) {
                    errBox.textContent = error.message || 'Error en el pago';
                }
            } catch (_) {
                errBox.textContent = 'No se pudo completar el pago.';
            }
            if (submitBtn) submitBtn.disabled = false;
        });
    }

    document.querySelectorAll('[data-offer-countdown]').forEach(function (box) {
        var raw = String(box.getAttribute('data-offer-countdown') || '').replace(' ', 'T');
        var label = box.querySelector('[data-offer-countdown-label]');
        var end = new Date(raw).getTime();
        function render() {
            var diff = end - Date.now();
            if (!label || !Number.isFinite(end)) return;
            if (diff <= 0) {
                label.textContent = 'Promoción finalizada';
                return;
            }
            var mins = Math.floor(diff / 60000);
            var days = Math.floor(mins / 1440);
            var hours = Math.floor((mins % 1440) / 60);
            var minutes = mins % 60;
            label.textContent = 'Quedan para comprar: ' + days + 'd ' + hours + 'h ' + minutes + 'm';
        }
        render();
        setInterval(render, 60000);
    });
})();
</script>
