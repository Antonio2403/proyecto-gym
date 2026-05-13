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
?>

<div class="container py-5">
    <?php if (!empty($_GET['info'])): ?>
        <div class="alert alert-info border-0 shadow-sm mb-4"><?= htmlspecialchars((string) $_GET['info']) ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-warning border-0 shadow-sm mb-4"><?= htmlspecialchars((string) $_GET['error']) ?></div>
    <?php endif; ?>

    <header class="gp-page-header text-center mb-4 mb-lg-5">
        <span class="gp-badge mb-2">Suscripciones</span>
        <h1 class="h2 mb-2">Planes Spartum</h1>
        <p class="text-muted mb-0 mx-auto gp-pago-lead">
            Comparativa clara de lo que incluye cada tarifa. El pago es seguro con Stripe.
            <?php if (!$esCliente): ?>
                <span class="d-block mt-2 small"><a href="<?= htmlspecialchars(url('/login')) ?>">Inicia sesión como cliente</a> para poder contratar un plan.</span>
            <?php endif; ?>
        </p>
    </header>

    <?php if ($planActivo): ?>
        <div class="gp-plan-current-banner gp-plan-current-banner--hero mb-4 mx-auto">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <span class="gp-badge gp-badge-soft mb-2 d-inline-block">Tu plan actual</span>
                    <?php if ((int) ($planActivo['en_oferta'] ?? 0) === 1): ?>
                        <span class="gp-badge gp-badge-soft mb-2 d-inline-block">Oferta contratada</span>
                    <?php endif; ?>
                    <h2 class="h5 mb-1 text-dark"><?= htmlspecialchars((string) ($planActivo['plan_nombre'] ?? 'Plan')) ?></h2>
                    <p class="text-muted small mb-0">
                        Vigente hasta:
                        <?= !empty($planActivo['fecha_fin'])
                            ? '<strong>' . htmlspecialchars(substr((string) $planActivo['fecha_fin'], 0, 16)) . '</strong>'
                            : '<strong>sin fecha de fin definida</strong>' ?>
                        · Mientras esté activo no puedes contratar otro plan diferente desde la web.
                    </p>
                    <div class="gp-current-plan-details mt-3">
                        <span><strong>Precio:</strong> <?= isset($planActivo['precio']) ? htmlspecialchars((string) $planActivo['precio']) . ' €' : 'no disponible' ?></span>
                        <span><strong>Duración contratada:</strong> <?= (int) ($planActivo['duracion'] ?? 0) ?> mes(es)</span>
                        <span><strong>Clases/semana:</strong> <?= (int) ($planActivo['numero_clases'] ?? 0) > 0 ? (int) $planActivo['numero_clases'] : 'sin límite explícito' ?></span>
                        <span><strong>Fisio:</strong> <?= (($planActivo['fisio'] ?? '') === 'S') ? 'incluida' : 'no incluida' ?></span>
                        <?php if (($planActivo['plan_catalogo_estado'] ?? 'A') !== 'A'): ?>
                            <span><strong>Catálogo:</strong> ya no disponible para nuevas compras</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>" class="btn btn-primary btn-sm">Ir a actividades</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
        <div>
            <h2 class="h4 mb-1">Planes disponibles</h2>
            <p class="text-muted small mb-0">
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
                <header class="gp-plan-card-head pb-3">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <h2 class="h4 mb-1"><?= htmlspecialchars((string) ($sub['nombre'] ?? 'Plan')) ?></h2>
                            <p class="small text-muted mb-0"><?= $esOferta ? 'Oferta por tiempo limitado' : 'Plan del catálogo' ?></p>
                        </div>
                        <div class="gp-plan-card-price text-end"><?= isset($sub['precio']) ? htmlspecialchars((string) $sub['precio']) . ' €' : '—' ?></div>
                    </div>
                    <?php if ($esOferta && !$retiradoCatalogo): ?>
                        <div class="gp-plan-offer-box mt-3" data-offer-countdown="<?= htmlspecialchars($ofertaFin) ?>">
                            <strong><?= htmlspecialchars($ofertaMotivo !== '' ? $ofertaMotivo : 'Oferta limitada') ?></strong>
                            <span class="d-block small">Disponible para comprar hasta: <?= htmlspecialchars(substr($ofertaFin, 0, 16)) ?></span>
                            <span class="d-block small" data-offer-countdown-label>Calculando tiempo restante...</span>
                        </div>
                    <?php endif; ?>
                </header>

                <div class="gp-plan-comparison small flex-grow-1">
                    <div><span>Duración</span><strong><?= $durMeses > 0 ? $durMeses . ' mes(es)' : 'no definida' ?></strong></div>
                    <div><span>Clases/semana</span><strong><?= $clasesPorSemana > 0 ? 'hasta ' . $clasesPorSemana : 'sin límite explícito' ?></strong></div>
                    <div><span>Fisioterapia</span><strong><?= $conFisio ? 'incluida' : 'no incluida' ?></strong></div>
                </div>

                <ul class="gp-plan-features list-unstyled small my-4">
                    <li><span class="gp-plan-ico" aria-hidden="true"></span> Reserva plaza en actividades desde la app web.</li>
                    <li><span class="gp-plan-ico" aria-hidden="true"></span> Atención en recepción y cambios tramitados en el centro.</li>
                </ul>

                <?php if ($bloqueadoPorOtroPlan): ?>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-secondary" disabled>Plan activo en tu cuenta</button>
                        <span class="text-center small text-muted">Para cambiar, cancelar o darte de baja debes contactar con recepción.</span>
                    </div>
                <?php elseif (!$esCliente): ?>
                    <div class="d-grid gap-2 mt-auto">
                        <a href="<?= htmlspecialchars(url('/login')) ?>" class="btn btn-primary fw-semibold">Inicia sesión para contratar</a>
                    </div>
                <?php elseif ($esActual): ?>
                    <button type="button" class="btn btn-success fw-semibold w-100" disabled>Tu suscripción vigente</button>
                    <p class="small text-muted text-center mb-0 mt-2">Vence el <?= !empty($planActivo['fecha_fin']) ? htmlspecialchars(substr((string) $planActivo['fecha_fin'], 0, 16)) : 'día indicado por el centro' ?>.</p>
                <?php else: ?>
                    <button type="button" class="btn btn-dark fw-semibold w-100"
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

    <div class="row justify-content-center mt-5">
        <div class="col-lg-6">
            <div id="payment-wrap" class="gp-pay-box card border-0 shadow-lg" style="display: none;">
                <div class="card-body p-4 p-lg-5">
                    <h3 class="h6 text-uppercase text-muted letter-spacing mb-3">Completa el pago con Stripe</h3>
                    <form id="payment-form">
                        <div id="payment-element"></div>
                        <button type="submit" id="submit" class="btn btn-success btn-lg w-100 mt-3">Pagar ahora</button>
                    </form>
                </div>
            </div>
            <p id="error-message" class="text-danger text-center small mt-3 mb-0"></p>
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
