<?php
$paso = (string) ($paso ?? '');
$telefonoMascara = (string) ($telefono_mascara ?? '');
$emailRevelado = (string) ($email_revelado ?? '');
$solicitudId = isset($solicitud_id) ? (int) $solicitud_id : 0;
$tipoTicket = (string) ($tipo_ticket ?? ($_GET['tipo'] ?? 'correo'));
if (!in_array($tipoTicket, ['correo', 'contrasena', 'reactivacion'], true)) {
    $tipoTicket = 'correo';
}
$tipoLabels = [
    'correo' => 'recuperar mi correo',
    'contrasena' => 'cambiar mi contraseña',
    'reactivacion' => 'reactivar mi cuenta',
];
$tipoTitulo = $tipoLabels[$tipoTicket] ?? $tipoLabels['correo'];
$crearAction = $tipoTicket === 'reactivacion' ? url('/reactivar-cuenta/solicitar') : url('/ticket/crear');
$ticketExpiraEn = (string) ($ticket_expira_en ?? '');
$codigoNecesitaDni = !empty($codigo_necesita_dni);
$ticketCodigoDesdeSesion = !empty($ticket_codigo_desde_sesion);
?>
<div class="gp-auth-shell gp-ticket-page py-5">
    <div class="container gp-auth-recovery-wrap">
        <div class="gp-auth-card gp-auth-card--wide p-4 p-lg-5 shadow-sm">
            <?php if ($paso === 'solicitud' && $solicitudId > 0): ?>
                <h1 class="h4 mb-2 text-center">Ticket creado</h1>
                <p class="text-muted small text-center mb-4">
                    Tu identidad con DNI y teléfono ya está validada. Falta ir a recepción para que el personal compruebe el caso
                    y te facilite el <strong class="text-body">código de 6 dígitos</strong>.
                    <?php if ($ticketExpiraEn !== ''): ?>
                        <span class="d-block mt-2">Este ticket caduca el <strong class="text-body"><?= htmlspecialchars($ticketExpiraEn) ?></strong> (48 horas desde su creación). Mientras esté activo no puedes abrir otro.</span>
                    <?php endif; ?>
                </p>
            <?php elseif ($codigoNecesitaDni): ?>
                <h1 class="h4 mb-2 text-center">Código de recepción</h1>
                <p class="text-muted small text-center mb-4">
                    Introduce el código de <strong>6 dígitos</strong> que te dio recepción.
                    Si abriste el ticket <strong>en este mismo navegador</strong>, vuelve atrás y entra por
                    <strong>«Ya tengo el código»</strong> en la página principal: el sistema pondrá solo el número de ticket.
                    Si cambiaste de dispositivo o borraste datos, indica también tu DNI y teléfono para localizar tu ticket pendiente (no hace falta saber el número).
                </p>
            <?php elseif ($paso === 'codigo' && $solicitudId > 0): ?>
                <h1 class="h4 mb-2 text-center">Código de recepción</h1>
                <p class="text-muted small text-center mb-4">
                    Escribe el código de <strong>6 dígitos</strong> para el ticket
                    <strong class="text-body">n.º <?= (int) $solicitudId ?></strong>.
                    <?php if ($ticketCodigoDesdeSesion): ?>
                        <span class="d-block mt-2">Este número se ha enlazado <strong class="text-body">automáticamente</strong> con el ticket que abriste en este dispositivo.</span>
                    <?php endif; ?>
                    <?php if ($telefonoMascara !== ''): ?>
                        <span class="d-block mt-2">Referencia del teléfono de tu ficha: <strong class="text-body"><?= htmlspecialchars($telefonoMascara) ?></strong>.</span>
                    <?php endif; ?>
                </p>
            <?php elseif ($paso === 'correo' && $emailRevelado !== ''): ?>
                <h1 class="h4 mb-2 text-center"><?= $tipoTicket === 'contrasena' ? 'Cambiar contraseña' : 'Correo de tu cuenta' ?></h1>
                <p class="text-muted small text-center mb-4">
                    <?= $tipoTicket === 'contrasena'
                        ? 'Tras validar el código, confirma el correo al que enviaremos el enlace para elegir una contraseña nueva.'
                        : 'Tras validar el código, ya podemos mostrarte el email registrado en tu cuenta.' ?>
                </p>
            <?php else: ?>
                <h1 class="h4 mb-2 text-center">Ticket de gestión de cuenta</h1>
                <p class="text-muted small text-center mb-4">
                    Solo puede haber <strong>un ticket abierto por cuenta</strong>: hasta que caduque (48 h), lo completes con el código o lo canceles, no podrás crear otro.
                    Si cancelas un ticket, deberás esperar <strong>48 horas</strong> para abrir uno nuevo.
                    Cuando tengas el código de recepción, usa <strong>«Ya tengo el código»</strong> (página aparte): en el mismo navegador donde creaste el ticket no tendrás que escribir el número.
                </p>
            <?php endif; ?>

            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-danger small mb-4"><?= htmlspecialchars((string) $_GET['error']) ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['success'])): ?>
                <div class="alert alert-success small mb-4"><?= htmlspecialchars((string) $_GET['success']) ?></div>
            <?php endif; ?>

            <?php if ($paso === 'solicitud' && $solicitudId > 0): ?>
                <div class="gp-recovery-solicitud-box text-center mb-4">
                    <div class="gp-recovery-solicitud-label mb-1">N.º de ticket para <?= htmlspecialchars($tipoTitulo) ?></div>
                    <div class="gp-recovery-solicitud-num" id="gp-ticket-num-display">#<?= (int) $solicitudId ?></div>
                    <button type="button" class="btn btn-outline-dark btn-sm mt-2" data-gp-copy-ticket-num="<?= (int) $solicitudId ?>">
                        <i class="far fa-copy me-1" aria-hidden="true"></i><span class="gp-copy-ticket-label">Copiar número de ticket</span>
                    </button>
                </div>
                <div class="gp-recovery-step mb-4">
                    <h2 class="h6 text-uppercase text-muted mb-3">Qué hacer ahora</h2>
                    <ol class="small mb-4 ps-3">
                        <li class="mb-2">Acude a recepción con este número de ticket.</li>
                        <li class="mb-2">El personal comprobará tu identidad y el motivo del ticket.</li>
                        <li class="mb-0">Cuando te den el código de 6 dígitos, pulsa <strong>«Ya tengo el código»</strong> en la página del ticket (o el botón de abajo) e introdúcelo; en este navegador no hace falta recordar el número.</li>
                    </ol>
                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <a class="btn btn-primary" href="<?= htmlspecialchars(url('/ticket') . '?paso=codigo') ?>">Ya tengo el código de recepción</a>
                        <a class="btn btn-outline-secondary" href="<?= htmlspecialchars(url('/login')) ?>">Volver al inicio de sesión</a>
                    </div>
                </div>
                <div class="alert alert-light border small mb-0">
                    <p class="mb-2"><strong>Cancelar ticket</strong>: si ya no lo necesitas, puedes cancelarlo aquí. Al cancelarlo deberás esperar <strong>48 horas</strong> antes de poder crear otro ticket.</p>
                    <form method="post" action="<?= htmlspecialchars(url('/ticket/cancelar')) ?>" class="mb-0"
                          onsubmit="return confirm('¿Cancelar este ticket? No podrás crear otro hasta dentro de 48 horas.');">
                        <input type="hidden" name="solicitud_id" value="<?= (int) $solicitudId ?>">
                        <button type="submit" class="btn btn-outline-danger btn-sm">Cancelar mi ticket</button>
                    </form>
                </div>

            <?php elseif ($codigoNecesitaDni): ?>
                <div class="gp-recovery-step mb-4">
                    <form method="post" action="<?= htmlspecialchars(url('/ticket/codigo-por-dni')) ?>" class="needs-validation" novalidate data-gp-validate="recoveryDniPhoneCode">
                        <div class="mb-3">
                            <label class="form-label" for="dni_codigo_only">DNI / NIE</label>
                            <input type="text" class="form-control form-control-lg font-monospace gp-doc-identidad-input" id="dni_codigo_only" name="dni" required maxlength="9" autocomplete="off" inputmode="text" placeholder="12345678A" data-gp-doc-identidad-es>
                            <p class="form-text small mb-0">8 números y una letra (DNI), o NIE: X, Y o Z, 7 números y letra. No se admiten más de 9 caracteres.</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="tel_codigo_only">Teléfono (9 dígitos)</label>
                            <input type="text" class="form-control form-control-lg" id="tel_codigo_only" name="telefono" required inputmode="numeric" placeholder="612 34 56 78" maxlength="12" pattern="[0-9]{3} [0-9]{2} [0-9]{2} [0-9]{2}" autocomplete="tel" data-gp-phone-input>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="codigo_verificacion_dni">Código de recepción</label>
                            <input type="text" class="form-control form-control-lg font-monospace text-center" id="codigo_verificacion_dni" name="codigo_verificacion" required maxlength="7" inputmode="numeric" pattern="[0-9]{3}-[0-9]{3}" autocomplete="one-time-code" placeholder="123-456" data-gp-recovery-code-input>
                            <p class="form-text small mb-0">6 dígitos; el guion se añade solo.</p>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-2">Verificar código</button>
                        <a href="<?= htmlspecialchars(url('/ticket')) ?>" class="btn btn-link w-100 small">Volver al ticket de cuenta</a>
                    </form>
                </div>

            <?php elseif ($paso === 'codigo' && $solicitudId > 0): ?>
                <div class="gp-recovery-step mb-4">
                    <h2 class="h6 text-uppercase text-muted mb-3">Verificación · Ticket n.º <?= (int) $solicitudId ?></h2>
                    <form method="post" action="<?= htmlspecialchars(url('/ticket/codigo')) ?>" class="needs-validation" novalidate data-gp-validate="recoveryDniCode">
                        <input type="hidden" name="solicitud_id" value="<?= (int) $solicitudId ?>">
                        <div class="mb-3">
                            <label class="form-label" for="codigo_verificacion">Código</label>
                            <input type="text" class="form-control form-control-lg font-monospace text-center tracking-wide" id="codigo_verificacion" name="codigo_verificacion"
                                   required maxlength="7" inputmode="numeric" pattern="[0-9]{3}-[0-9]{3}" autocomplete="one-time-code" placeholder="123-456" aria-describedby="codigo_verificacion_help" data-gp-recovery-code-input>
                            <p id="codigo_verificacion_help" class="form-text small mb-0">Escribe 3 números, el guion se añade solo, y luego otros 3 números.</p>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-2">Continuar</button>
                        <a href="<?= htmlspecialchars(url('/ticket')) ?>" class="btn btn-link w-100 small">Volver al inicio del ticket</a>
                    </form>
                    <p class="text-center small text-muted mb-0 mt-2">
                        <a href="<?= htmlspecialchars(url('/ticket') . '?paso=codigo&forzar_identidad=1') ?>">Otro dispositivo o datos borrados: identificar con DNI y teléfono</a>
                    </p>
                </div>

            <?php elseif ($paso === 'correo' && $emailRevelado !== ''): ?>
                <div class="gp-recovery-step mb-4">
                    <h2 class="h6 text-uppercase text-muted mb-3">Tu correo registrado</h2>
                    <p class="small mb-3">
                        <?= $tipoTicket === 'contrasena'
                            ? 'Este es el email asociado a tu cuenta. Si es correcto, solicita el enlace para actualizar tu contraseña.'
                            : 'Este es el email asociado a tu cuenta. Guárdalo para iniciar sesión o para crear otro ticket si necesitas cambiar la contraseña.' ?>
                    </p>
                    <div class="alert alert-secondary font-monospace text-break mb-4"><?= htmlspecialchars($emailRevelado) ?></div>
                    <?php if ($tipoTicket === 'contrasena'): ?>
                        <form method="post" action="<?= htmlspecialchars(url('/recuperar-contrasena/dni/enviar-enlace')) ?>" class="mb-3">
                            <button type="submit" class="btn btn-primary w-100">Enviar enlace para cambiar contraseña</button>
                        </form>
                    <?php endif; ?>
                    <p class="text-center small mb-0"><a href="<?= htmlspecialchars(url('/login')) ?>">Volver al inicio de sesión</a></p>
                </div>

            <?php else: ?>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <a class="gp-ticket-option card h-100 text-decoration-none shadow-sm <?= $tipoTicket === 'correo' ? 'border border-primary' : 'border-0' ?>" href="<?= htmlspecialchars(url('/ticket') . '?tipo=correo') ?>">
                            <div class="card-body">
                                <span class="badge bg-primary-subtle text-primary mb-2">Correo</span>
                                <h2 class="h6 text-body">No sé mi correo</h2>
                                <p class="small text-muted mb-0">Crea un ticket con DNI/teléfono para que recepción te dé el código y puedas ver el email registrado.</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a class="gp-ticket-option card h-100 text-decoration-none shadow-sm <?= $tipoTicket === 'contrasena' ? 'border border-primary' : 'border-0' ?>" href="<?= htmlspecialchars(url('/ticket') . '?tipo=contrasena') ?>">
                            <div class="card-body">
                                <span class="badge bg-warning-subtle text-warning-emphasis mb-2">Contraseña</span>
                                <h2 class="h6 text-body">No recuerdo mi contraseña</h2>
                                <p class="small text-muted mb-0">Crea un ticket para validar tu identidad y recibir después el enlace para actualizar la contraseña.</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a class="gp-ticket-option card h-100 text-decoration-none shadow-sm <?= $tipoTicket === 'reactivacion' ? 'border border-primary' : 'border-0' ?>" href="<?= htmlspecialchars(url('/ticket') . '?tipo=reactivacion') ?>">
                            <div class="card-body">
                                <span class="badge bg-info-subtle text-info-emphasis mb-2">Reactivación</span>
                                <h2 class="h6 text-body">Mi cuenta está de baja</h2>
                                <p class="small text-muted mb-0">Solo para bajas normales. Crea el ticket y recepción te facilitará el código de reactivación.</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="gp-recovery-step mb-4">
                    <h2 class="h6 text-uppercase text-muted mb-3">Abrir ticket para <?= htmlspecialchars($tipoTitulo) ?></h2>
                    <p class="small text-muted mb-3">
                        Indica tu <strong>DNI o NIE</strong> y el <strong>teléfono móvil</strong> asociado a tu ficha. Si coinciden, se creará un ticket para recepción.
                    </p>
                    <form method="post" action="<?= htmlspecialchars($crearAction) ?>" class="needs-validation" novalidate data-gp-validate="recoveryDniPhone">
                        <?php if ($tipoTicket !== 'reactivacion'): ?>
                            <input type="hidden" name="tipo_ticket" value="<?= htmlspecialchars($tipoTicket) ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label" for="dni_rec">DNI / NIE</label>
                            <input type="text" class="form-control form-control-lg font-monospace gp-doc-identidad-input" id="dni_rec" name="dni" required maxlength="9" autocomplete="off" inputmode="text" placeholder="12345678A" data-gp-doc-identidad-es>
                            <p class="form-text small mb-0">8 números y una letra (DNI), o NIE: X, Y o Z, 7 números y letra. Máximo 9 caracteres.</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="tel_rec">Teléfono (9 dígitos)</label>
                            <input type="text" class="form-control form-control-lg" id="tel_rec" name="telefono" required inputmode="numeric" placeholder="612 34 56 78" maxlength="12" pattern="[0-9]{3} [0-9]{2} [0-9]{2} [0-9]{2}" autocomplete="tel" data-gp-phone-input>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Crear ticket</button>
                    </form>
                    <div class="d-grid gap-2 mt-3">
                        <a class="btn btn-primary w-100" href="<?= htmlspecialchars(url('/ticket') . '?paso=codigo') ?>">Ya tengo el código de recepción</a>
                        <p class="small text-muted text-center mb-0">Si creaste el ticket en este navegador, no hace falta que recuerdes el número: se enlaza solo.</p>
                    </div>
                </div>

                <p class="text-center small mb-0 mt-3"><a href="<?= htmlspecialchars(url('/login')) ?>">Volver al inicio de sesión</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
(function () {
    function formatRecoveryCode(raw) {
        var t = String(raw || '').replace(/[^0-9]/g, '').slice(0, 6);
        if (!t.length) {
            return '';
        }
        if (t.length <= 3) {
            return t;
        }
        return t.slice(0, 3) + '-' + t.slice(3, 6);
    }
    document.querySelectorAll('[data-gp-recovery-code-input]').forEach(function (el) {
        el.addEventListener('paste', function (ev) {
            var txt = '';
            try {
                txt = (ev.clipboardData || window.clipboardData).getData('text') || '';
            } catch (e) {
                return;
            }
            ev.preventDefault();
            el.value = formatRecoveryCode(txt);
            try {
                el.dispatchEvent(new Event('input', { bubbles: true }));
            } catch (e2) {
            }
        });
        el.addEventListener('input', function () {
            el.value = formatRecoveryCode(el.value);
        });
        el.addEventListener('blur', function () {
            el.value = formatRecoveryCode(el.value);
        });
    });
})();
</script>
<script>
(function () {
    document.querySelectorAll('[data-gp-copy-ticket-num]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var raw = String(btn.getAttribute('data-gp-copy-ticket-num') || '').trim();
            var labelEl = btn.querySelector('.gp-copy-ticket-label');
            var labelText = labelEl ? labelEl.textContent : 'Copiar número de ticket';
            function setDone() {
                if (labelEl) labelEl.textContent = 'Copiado';
            }
            function reset() {
                if (labelEl) labelEl.textContent = labelText;
            }
            if (!raw) return;
            function fallbackCopy(text) {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.setAttribute('readonly', '');
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.select();
                try { document.execCommand('copy'); } finally { document.body.removeChild(ta); }
            }
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(raw).then(function () {
                    setDone();
                    setTimeout(reset, 2000);
                }).catch(function () {
                    try {
                        fallbackCopy(raw);
                        setDone();
                        setTimeout(reset, 2000);
                    } catch (e) {
                        window.prompt('Copia el número de ticket:', raw);
                    }
                });
            } else {
                try {
                    fallbackCopy(raw);
                    setDone();
                    setTimeout(reset, 2000);
                } catch (e2) {
                    window.prompt('Copia el número de ticket:', raw);
                }
            }
        });
    });
})();
</script>
