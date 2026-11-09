<div class="content-wrapper">
    <div class="relative w-full px-3 mt-6">
        <header class="gp-page-header mb-6">
            <div class="gp-page-header__title">
                <h2 class="text-2xl mb-1">Usuarios activos</h2>
                <p class="text-slate-500 text-sm">Cuentas sin baneo. Desde aquí se gestionan el plan y las bajas.</p>
            </div>
        </header>

        <div
            class="gp-admin-grid gp-admin-card-panel"
            data-gp-admin-grid="clientes"
            data-lista="activos"
            data-endpoint="<?= htmlspecialchars(url('/admin/ajax/clientes')) ?>"
            data-colspan="9"
            data-url-cancel-plan="<?= htmlspecialchars(url('/admin/clientes/cancelar-plan')) ?>"
            data-url-block="<?= htmlspecialchars(url('/admin/clientes/bloquear')) ?>"
            data-url-unblock="<?= htmlspecialchars(url('/admin/clientes/desbloquear')) ?>">

            <form class="gp-admin-grid-filters items-end mb-4 flex flex-wrap -mx-1 -mt-2" data-grid-filters novalidate>
                <div class="w-full lg:w-5/12 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Buscar global</label>
                    <input type="search" class="form-control form-control-sm" name="q" placeholder="Nombre, email, DNI…" autocomplete="off">
                </div>
                <div class="w-full md:w-1/4 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">DNI</label>
                    <input type="text" class="form-control form-control-sm font-mono gp-doc-identidad-input" name="dni" maxlength="9" autocomplete="off" inputmode="text" placeholder="12345678A" data-gp-doc-identidad-es>
                </div>
                <div class="w-full md:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Email</label>
                    <input type="text" class="form-control form-control-sm" name="email" autocomplete="off">
                </div>
                <div class="w-full md:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Nombre o apellidos</label>
                    <input type="text" class="form-control form-control-sm" name="nombre" autocomplete="off">
                </div>
                <div class="w-full md:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Teléfono</label>
                    <input type="text" class="form-control form-control-sm" name="telefono" autocomplete="off">
                </div>
                <div class="w-full md:w-1/3 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Método de pago</label>
                    <input type="text" class="form-control form-control-sm" name="metodo_pago" placeholder="Ej. tarjeta" autocomplete="off">
                </div>
                <div class="flex flex-wrap gap-2 mt-2 w-full lg:w-full max-w-full shrink-0 px-1">
                    <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-grid-reset>Limpiar</button>
                    <div class="lg:ms-auto flex items-center gap-2">
                        <label class="text-sm text-slate-500 mb-0">Por página</label>
                        <select class="form-select form-select-sm" data-grid-per-page style="width: auto;">
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                        </select>
                    </div>
                </div>
            </form>

            <div class="flex justify-between items-center flex-wrap gap-2 mb-2">
                <small class="text-slate-500" data-grid-status>&nbsp;</small>
                <div data-grid-pagination aria-label="Paginación de clientes"></div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0 gp-data-table">
                    <thead>
                        <tr>
                            <th>ID cliente</th>
                            <th>DNI</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Método pago</th>
                            <th>Plan activo</th>
                            <th>Estado cuenta</th>
                            <th class="gp-actions-col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody data-grid-body>
                        <tr><td colspan="9" class="text-slate-500 py-6">Cargando…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <header class="gp-page-header mb-6 mt-8">
            <div class="gp-page-header__title">
                <h2 class="text-2xl mb-1">Usuarios no activos</h2>
                <p class="text-slate-500 text-sm">Cuentas baneadas, con el motivo y la opción de desbanear.</p>
            </div>
        </header>

        <div
            class="gp-admin-grid gp-admin-card-panel"
            data-gp-admin-grid="clientes"
            data-lista="no_activos"
            data-endpoint="<?= htmlspecialchars(url('/admin/ajax/clientes')) ?>"
            data-colspan="8"
            data-url-unblock="<?= htmlspecialchars(url('/admin/clientes/desbloquear')) ?>">

            <form class="gp-admin-grid-filters items-end mb-4 flex flex-wrap -mx-1 -mt-2" data-grid-filters novalidate>
                <div class="w-full lg:w-1/2 max-w-full shrink-0 px-1 mt-2">
                    <label class="form-label text-sm text-slate-500 mb-0">Buscar</label>
                    <input type="search" class="form-control form-control-sm" name="q" placeholder="Nombre, email, DNI…" autocomplete="off">
                </div>
                <div class="flex flex-wrap gap-2 mt-2 w-full max-w-full shrink-0 px-1">
                    <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-grid-reset>Limpiar</button>
                </div>
            </form>

            <div class="flex justify-between items-center flex-wrap gap-2 mb-2">
                <small class="text-slate-500" data-grid-status>&nbsp;</small>
                <div data-grid-pagination aria-label="Paginación de usuarios no activos"></div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0 gp-data-table">
                    <thead>
                        <tr>
                            <th>ID cliente</th>
                            <th>DNI</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Motivo del baneo</th>
                            <th>Tipo</th>
                            <th class="gp-actions-col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody data-grid-body>
                        <tr><td colspan="8" class="text-slate-500 py-6">Cargando…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="gpBanMotivoModal" tabindex="-1" aria-hidden="true" aria-labelledby="gpBanMotivoTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="gpBanMotivoForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="gpBanMotivoTitle">Banear usuario</h5>
                    <button type="button" class="btn-close" data-gp-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label gp-label-required" for="gpBanMotivo">Motivo del baneo</label>
                    <textarea class="form-control" id="gpBanMotivo" name="motivo" rows="4" maxlength="255" required placeholder="Escribe por qué se banea esta cuenta"></textarea>
                    <p class="form-text text-sm text-slate-500 mb-0">Este texto lo verá la persona si intenta entrar.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-gp-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Banear</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('gpBanMotivoForm');
    var modal = document.getElementById('gpBanMotivoModal');
    if (!form || !modal) {
        return;
    }
    form.addEventListener('submit', function (ev) {
        ev.preventDefault();
        var motivo = String((form.querySelector('[name="motivo"]') || {}).value || '').trim();
        if (motivo === '') {
            return;
        }
        var url = form.getAttribute('data-action') || '';
        var clienteId = form.getAttribute('data-cliente-id') || '';
        if (!url || !clienteId) {
            return;
        }
        if (window.SpartumUI && SpartumUI.Modal) {
            var inst = SpartumUI.Modal.getOrCreateInstance(modal);
            if (inst) {
                inst.hide();
            }
        }
        var body = new FormData();
        body.append('cliente_id', clienteId);
        body.append('tipo', 'P');
        body.append('motivo', motivo);
        var token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            body.append('csrf_token', token.getAttribute('content') || '');
        }
        fetch(url, { method: 'POST', body: body, credentials: 'same-origin', headers: { Accept: 'text/html' } })
            .then(function (r) { return r.text().then(function (html) { return { url: r.url, html: html }; }); })
            .then(function (res) {
                if (window.gpApplyAdminResult) {
                    window.gpApplyAdminResult(res);
                    return;
                }
                window.location.href = res.url || url;
            })
            .catch(function () {
                window.location.href = url;
            });
    });
});
</script>
