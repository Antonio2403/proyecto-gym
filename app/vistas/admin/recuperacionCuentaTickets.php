<div class="content-wrapper gp-admin-tickets-page">
    <div class="container-fluid mt-4"
         data-gp-admin-recuperacion-tickets-sync
         data-gp-endpoint="<?= htmlspecialchars(url('/admin/ajax/recuperacion-tickets-firma'), ENT_QUOTES, 'UTF-8') ?>"
         data-gp-firma="<?= htmlspecialchars((string) ($firma_tickets ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <div class="gp-admin-card-panel gp-admin-tickets-hero border-0 shadow-sm mb-4">
            <div class="d-flex flex-wrap gap-2 align-items-start justify-content-between">
            <div class="me-auto">
                <span class="gp-badge mb-2 d-inline-block">Recepción</span>
                <h2 class="mb-1 h4 text-dark">Tickets · gestión de cuenta</h2>
                <p class="text-muted small mb-0">
                    El socio valida <strong>DNI y teléfono</strong> en la web; en recepción comprueba la identidad y <strong>dicta el código</strong>.
                    El tipo indica si quiere recuperar correo, cambiar contraseña o reactivar una baja normal.
                    Cada ticket caduca a las <strong>48 horas</strong> desde su creación. Solo puede haber <strong>un ticket pendiente por usuario</strong>; si el socio cancela el suyo en la web, debe esperar <strong>48 horas</strong> para abrir otro.
                </p>
                <p class="small text-muted mb-0 mt-1"><i class="fas fa-rotate me-1 opacity-75" aria-hidden="true"></i>La vista se actualiza sola si cambia la lista (ticket nuevo, cerrado, completado por el socio o caducado).</p>
            </div>
            <div class="gp-view-toolbar align-self-start">
            <a href="<?= htmlspecialchars(url('/admin')) ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Panel
            </a>
            </div>
            </div>
        </div>

        <ul class="nav nav-pills gap-2 mb-4 gp-admin-pill-tabs flex-wrap" id="ticketsRecTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-3" id="tab-pendientes" data-bs-toggle="pill" data-bs-target="#pane-pendientes" type="button" role="tab">
                    Abiertos <span class="badge bg-warning text-dark ms-1"><?= count($tickets_pendientes ?? []) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3" id="tab-historial" data-bs-toggle="pill" data-bs-target="#pane-historial" type="button" role="tab">
                    Historial
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="pane-pendientes" role="tabpanel">
                <?php if (empty($tickets_pendientes)): ?>
                    <div class="gp-ticket-empty text-center text-muted py-5 rounded-3 border border-dashed">
                        <i class="fas fa-ticket fa-2x mb-3 opacity-50"></i>
                        <p class="mb-0">No hay tickets abiertos. Aparecerán aquí cuando un socio cree un ticket desde /ticket.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($tickets_pendientes as $t): ?>
                            <?php
                            $nombreCompleto = trim(
                                ($t['nombre'] ?? '') . ' ' . ($t['apellido1'] ?? '') . ' ' . ($t['apellido2'] ?? '')
                            );
                            $codigoRaw = (string) ($t['codigo'] ?? '');
                            $tipoTicket = (string) ($t['tipo'] ?? 'correo');
                            if ($tipoTicket === 'contrasena') {
                                $tipoLabel = 'Cambiar contraseña';
                                $tipoBadge = 'bg-warning text-dark';
                            } elseif ($tipoTicket === 'reactivacion') {
                                $tipoLabel = 'Reactivar cuenta';
                                $tipoBadge = 'bg-info text-dark';
                            } else {
                                $tipoLabel = 'Recuperar correo';
                                $tipoBadge = 'bg-secondary';
                            }
                            $codigoSinGuion = preg_replace('/[^0-9]/', '', $codigoRaw) ?? '';
                            $codigoCopiarAttr = htmlspecialchars($codigoSinGuion, ENT_QUOTES, 'UTF-8');
                            $codigoPartes = explode('-', $codigoRaw, 2);
                            $c0 = htmlspecialchars($codigoPartes[0] ?? '', ENT_QUOTES, 'UTF-8');
                            $c1 = htmlspecialchars($codigoPartes[1] ?? '', ENT_QUOTES, 'UTF-8');
                            ?>
                            <div class="col-12 col-xl-6">
                                <div class="card h-100 gp-ticket-card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap align-items-start gap-2 mb-2">
                                            <span class="gp-ticket-id font-monospace fw-semibold">#<?= (int) $t['id'] ?></span>
                                            <span class="badge bg-warning text-dark">Abierto</span>
                                            <span class="badge <?= htmlspecialchars($tipoBadge) ?>"><?= htmlspecialchars($tipoLabel) ?></span>
                                            <span class="small text-muted ms-auto text-nowrap">
                                                <i class="far fa-clock me-1"></i><?= htmlspecialchars((string) $t['creado_en']) ?>
                                            </span>
                                        </div>
                                        <h3 class="h6 mb-2"><?= htmlspecialchars($nombreCompleto !== '' ? $nombreCompleto : 'Sin nombre') ?></h3>
                                        <dl class="row small mb-3 gx-2 gy-1">
                                            <dt class="col-sm-3 text-muted">DNI</dt>
                                            <dd class="col-sm-9 font-monospace mb-0"><?= htmlspecialchars((string) ($t['DNI'] ?? '')) ?></dd>
                                            <dt class="col-sm-3 text-muted">Email</dt>
                                            <dd class="col-sm-9 text-break mb-0"><?= htmlspecialchars((string) ($t['email'] ?? '')) ?></dd>
                                            <dt class="col-sm-3 text-muted">Teléfono</dt>
                                            <dd class="col-sm-9 mb-0"><?= htmlspecialchars((string) ($t['telefono'] ?? '')) ?></dd>
                                            <dt class="col-sm-3 text-muted">Caduca</dt>
                                            <dd class="col-sm-9 mb-0"><?= htmlspecialchars((string) $t['expira_en']) ?></dd>
                                        </dl>

                                        <div class="gp-ticket-code-wrap">
                                            <div class="fw-semibold small text-uppercase text-muted mb-2">
                                                <i class="fas fa-key me-1 text-warning"></i> Código para el socio (recepción)
                                            </div>
                                            <div class="gp-recovery-code-display" role="status" aria-label="Código de verificación">
                                                <span class="gp-recovery-code-chunk"><?= $c0 !== '' ? $c0 : htmlspecialchars($codigoRaw, ENT_QUOTES, 'UTF-8') ?></span>
                                                <?php if ($c1 !== ''): ?>
                                                    <span class="gp-recovery-code-sep" aria-hidden="true">-</span>
                                                    <span class="gp-recovery-code-chunk"><?= $c1 ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="gp-ticket-code-hint mb-2">
                                                Dicta este código al socio o muéstralo en pantalla. Con el <strong>n.º de ticket <?= (int) $t['id'] ?></strong> y este código puede confirmar la gestión desde <span class="font-monospace">/ticket</span>.
                                            </p>
                                            <p class="gp-ticket-code-hint mb-2">
                                                <strong>Copiar al portapapeles:</strong> se guardan solo los 6 números (sin guion). Al pegarlos en la web se colocan como <span class="font-monospace">123-456</span>.
                                            </p>
                                            <button type="button" class="btn btn-outline-dark btn-sm" data-gp-copy-ticket-code="<?= $codigoCopiarAttr ?>">
                                                <i class="far fa-copy me-1" aria-hidden="true"></i><span class="gp-copy-btn-label">Copiar código (sin guion)</span>
                                            </button>
                                        </div>

                                        <?php if ($tipoTicket === 'contrasena'): ?>
                                            <form method="post" action="<?= htmlspecialchars(url('/admin/recuperacion-cuenta/enviar-correo')) ?>" class="d-grid gap-2 mt-3 pt-3 border-top">
                                                <input type="hidden" name="ticket_id" value="<?= (int) $t['id'] ?>">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-envelope me-1"></i> Enviar enlace para cambiar contraseña
                                                </button>
                                                <p class="small text-muted mb-0">Opcional si quieres enviar desde el panel el enlace seguro de cambio de contraseña. El código de recepción no cambia.</p>
                                            </form>
                                        <?php endif; ?>
                                        <form method="post" action="<?= htmlspecialchars(url('/admin/recuperacion-cuenta/cerrar-ticket')) ?>" class="d-grid gap-2 mt-2"
                                              onsubmit="return confirm('¿Cerrar este ticket? El socio ya no podrá usar este código ni esta solicitud (podrá abrir una nueva si lo necesita).');">
                                            <input type="hidden" name="ticket_id" value="<?= (int) $t['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-times-circle me-1"></i> Cerrar ticket
                                            </button>
                                            <p class="small text-muted mb-0">Úsalo si el caso queda resuelto en recepción, si fue un error o si no debía abrirse. El ticket pasa al historial.</p>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="pane-historial" role="tabpanel">
                <?php if (empty($tickets_historial)): ?>
                    <p class="text-muted small">Aún no hay tickets cerrados en el historial reciente.</p>
                <?php else: ?>
                    <div class="table-responsive gp-admin-card-panel rounded shadow-sm">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ticket</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>DNI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets_historial as $h): ?>
                                    <?php
                                    $nomH = trim(
                                        ($h['nombre'] ?? '') . ' ' . ($h['apellido1'] ?? '') . ' ' . ($h['apellido2'] ?? '')
                                    );
                                    $est = (string) ($h['estado'] ?? '');
                                    if ($est === 'usado') {
                                        $estLabel = 'Completado (web)';
                                        $badge = 'success';
                                    } elseif ($est === 'cerrado_por_admin') {
                                        $estLabel = 'Cerrado (admin)';
                                        $badge = 'info';
                                    } elseif ($est === 'cancelado') {
                                        $estLabel = 'Cancelado';
                                        $badge = 'secondary';
                                    } else {
                                        $estLabel = $est;
                                        $badge = 'secondary';
                                    }
                                    ?>
                                    <tr>
                                        <td class="font-monospace">#<?= (int) $h['id'] ?></td>
                                        <?php
                                        $histTipo = (string) ($h['tipo'] ?? 'correo');
                                        if ($histTipo === 'contrasena') {
                                            $histTipoLabel = 'Cambiar contraseña';
                                        } elseif ($histTipo === 'reactivacion') {
                                            $histTipoLabel = 'Reactivar cuenta';
                                        } else {
                                            $histTipoLabel = 'Recuperar correo';
                                        }
                                        ?>
                                        <td class="small"><?= htmlspecialchars($histTipoLabel) ?></td>
                                        <td><span class="badge bg-<?= htmlspecialchars($badge) ?>"><?= htmlspecialchars($estLabel) ?></span></td>
                                        <td class="small text-nowrap"><?= htmlspecialchars((string) $h['creado_en']) ?></td>
                                        <td class="small"><?= htmlspecialchars($nomH !== '' ? $nomH : '—') ?></td>
                                        <td class="font-monospace small"><?= htmlspecialchars((string) ($h['DNI'] ?? '')) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    document.querySelectorAll('[data-gp-copy-ticket-code]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var raw = btn.getAttribute('data-gp-copy-ticket-code') || '';
            var labelEl = btn.querySelector('.gp-copy-btn-label');
            var labelText = labelEl ? labelEl.textContent : 'Copiar código (sin guion)';
            function setDone() {
                if (labelEl) {
                    labelEl.textContent = 'Copiado';
                }
            }
            function reset() {
                if (labelEl) {
                    labelEl.textContent = labelText;
                }
            }
            if (!raw) {
                return;
            }
            function fallbackCopy(text) {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.setAttribute('readonly', '');
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.select();
                try {
                    document.execCommand('copy');
                } finally {
                    document.body.removeChild(ta);
                }
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
                        window.prompt('Copia el código manualmente:', raw);
                    }
                });
            } else {
                try {
                    fallbackCopy(raw);
                    setDone();
                    setTimeout(reset, 2000);
                } catch (e2) {
                    window.prompt('Copia el código manualmente:', raw);
                }
            }
        });
    });
})();

(function () {
    var root = document.querySelector('[data-gp-admin-recuperacion-tickets-sync]');
    if (!root) {
        return;
    }
    var endpoint = root.getAttribute('data-gp-endpoint') || '';
    var firma = root.getAttribute('data-gp-firma') || '';
    if (!endpoint) {
        return;
    }
    var tabKey = 'gpAdminRecTicketsActiveTab';
    var hidden = false;
    document.addEventListener('visibilitychange', function () {
        hidden = document.visibilityState !== 'visible';
    });
    function saveActiveTab() {
        try {
            var active = document.querySelector('#ticketsRecTabs button.nav-link.active');
            if (active && active.getAttribute('data-bs-target')) {
                sessionStorage.setItem(tabKey, active.getAttribute('data-bs-target') || '');
            }
        } catch (e) { /* ignore */ }
    }
    function restoreTab() {
        var sel = sessionStorage.getItem(tabKey);
        sessionStorage.removeItem(tabKey);
        if (!sel || typeof window.bootstrap === 'undefined') {
            return;
        }
        var btn = null;
        document.querySelectorAll('#ticketsRecTabs [data-bs-target]').forEach(function (b) {
            if (b.getAttribute('data-bs-target') === sel) {
                btn = b;
            }
        });
        if (btn) {
            try {
                window.bootstrap.Tab.getOrCreateInstance(btn).show();
            } catch (e2) { /* ignore */ }
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', restoreTab);
    } else {
        restoreTab();
    }
    function poll() {
        if (hidden) {
            return;
        }
        fetch(endpoint, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
            .then(function (r) {
                return r.json();
            })
            .then(function (j) {
                if (!j || j.ok !== true || typeof j.firma !== 'string') {
                    return;
                }
                if (j.firma !== firma) {
                    saveActiveTab();
                    window.location.reload();
                }
            })
            .catch(function () { /* red silenciosa */ });
    }
    setInterval(poll, 4500);
    setTimeout(poll, 1200);
})();
</script>
