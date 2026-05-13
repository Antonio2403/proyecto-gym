/**
 * Tablas admin: paginación + filtros vía fetch (JSON).
 */
(function () {
    'use strict';

    var DIAS = { L: 'Lunes', M: 'Martes', X: 'Miércoles', J: 'Jueves', V: 'Viernes', S: 'Sábado', D: 'Domingo' };

    function debounce(fn, ms) {
        var t;
        return function () {
            clearTimeout(t);
            var a = arguments;
            var th = this;
            t = setTimeout(function () {
                fn.apply(th, a);
            }, ms);
        };
    }

    function textCell(tr, t) {
        var td = document.createElement('td');
        td.textContent = t == null ? '' : String(t);
        tr.appendChild(td);
    }

    function truncate(s, max) {
        s = s == null ? '' : String(s);
        if (s.length <= max) {
            return s;
        }

        return s.slice(0, max) + '…';
    }

    function timeFromDt(dt) {
        if (!dt) {
            return '';
        }
        var m = String(dt).match(/(\d{2}:\d{2})/);

        return m ? m[1] : '';
    }

    function nombreCliente(r) {
        return [r.nombre, r.apellido1, r.apellido2].filter(Boolean).join(' ').trim();
    }

    function nombreMonitorSolicitud(r) {
        return [r.monitor_nombre, r.monitor_apellido1, r.monitor_apellido2].filter(Boolean).join(' ').trim();
    }

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? String(meta.getAttribute('content') || '') : '';
    }

    function submitPost(href, fields) {
        var form = document.createElement('form');
        form.method = 'post';
        form.action = href;
        Object.keys(fields || {}).forEach(function (name) {
            var hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = name;
            hidden.value = fields[name];
            form.appendChild(hidden);
        });
        var token = csrfToken();
        if (token) {
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'csrf_token';
            inp.value = token;
            form.appendChild(inp);
        }
        document.body.appendChild(form);
        form.submit();
    }

    function actionCls(kind) {
        var base = 'btn btn-sm gp-btn-action ';
        var map = {
            edit: base + 'gp-btn-action--edit',
            ok: base + 'gp-btn-action--ok',
            warn: base + 'gp-btn-action--warn',
            danger: base + 'gp-btn-action--danger',
            neutral: base + 'gp-btn-action--neutral',
            reply: base + 'gp-btn-action--reply',
        };

        return map[kind] || map.neutral;
    }

    function actionsCell(useCol) {
        var td = document.createElement('td');
        td.className = 'gp-actions-cell';
        var stack = document.createElement('div');
        stack.className = 'gp-actions-stack' + (useCol ? ' gp-actions-stack--col' : '');
        td.appendChild(stack);

        return { td: td, stack: stack };
    }

    function statusPill(kind, label) {
        var span = document.createElement('span');
        var map = {
            active: 'gp-status-pill--active',
            temp: 'gp-status-pill--temp',
            banned: 'gp-status-pill--banned',
            pending: 'gp-status-pill--pending',
            ok: 'gp-status-pill--ok',
            reject: 'gp-status-pill--reject',
        };
        span.className = 'gp-status-pill ' + (map[kind] || 'gp-status-pill--neutral');
        span.textContent = label;

        return span;
    }

    function appendAction(stack, node) {
        stack.appendChild(node);
    }

    function linkBtn(href, cls, label, confirmMsg, method, fields) {
        var a = document.createElement('a');
        a.href = href;
        a.className = cls;
        a.textContent = label;
        method = String(method || 'get').toLowerCase();
        if (confirmMsg) {
            a.addEventListener('click', function (e) {
                e.preventDefault();
                if (typeof window.gpConfirm === 'function') {
                    window.gpConfirm({
                        title: label === 'Baja normal' ? 'Condiciones de baja normal' : 'Confirmar acción',
                        body: confirmMsg,
                        danger: true,
                        okLabel: label === 'Baja normal' ? 'He leído las condiciones al cliente' : 'Confirmar',
                        onConfirm: function () {
                            if (method === 'post') {
                                submitPost(href, fields);
                            } else {
                                window.location.href = href;
                            }
                        },
                    });

                    return;
                }
                if (window.confirm(confirmMsg)) {
                    if (method === 'post') {
                        submitPost(href, fields);
                    } else {
                        window.location.href = href;
                    }
                }
            });
        }

        return a;
    }

    function buildRows(kind, rows, root) {
        var frag = document.createDocumentFragment();

        if (!rows || rows.length === 0) {
            var tr0 = document.createElement('tr');
            var colspan = parseInt(root.getAttribute('data-colspan') || '1', 10);
            var td0 = document.createElement('td');
            td0.colSpan = colspan;
            td0.className = 'text-center text-muted py-4';
            td0.textContent = 'Sin resultados con los filtros actuales.';
            tr0.appendChild(td0);
            frag.appendChild(tr0);

            return frag;
        }

        if (kind === 'clientes') {
            var cancelPlanUrl = root.getAttribute('data-url-cancel-plan') || '';
            var blockUrl = root.getAttribute('data-url-block') || '';
            var unblockUrl = root.getAttribute('data-url-unblock') || '';
            rows.forEach(function (r) {
                var tr = document.createElement('tr');
                textCell(tr, r.cliente_id);
                textCell(tr, r.DNI);
                textCell(tr, nombreCliente(r));
                textCell(tr, r.email);
                textCell(tr, r.telefono);
                textCell(tr, r.metodo_pago || '—');
                textCell(tr, r.plan_nombre ? (r.plan_nombre + (r.plan_fecha_fin ? ' · hasta ' + String(r.plan_fecha_fin).slice(0, 16) : '')) : '—');
                var tdEstado = document.createElement('td');
                tdEstado.className = 'gp-status-cell';
                if (r.bloqueo_tipo === 'P') {
                    tdEstado.appendChild(statusPill('banned', 'Baja permanente'));
                } else if (r.bloqueo_tipo === 'T') {
                    tdEstado.appendChild(statusPill('temp', 'Baja normal'));
                } else {
                    tdEstado.appendChild(statusPill('active', 'Activa'));
                }
                tr.appendChild(tdEstado);

                var act = actionsCell(true);
                if (cancelPlanUrl && r.plan_rel_id) {
                    appendAction(act.stack, linkBtn(cancelPlanUrl, actionCls('warn'), 'Cancelar plan', '¿Cancelar el plan activo de este cliente? Esta acción debe hacerse solo si recepción lo ha confirmado.', 'post', { cliente_id: r.cliente_id }));
                }
                if (unblockUrl && r.bloqueo_tipo === 'T') {
                    appendAction(act.stack, linkBtn(unblockUrl, actionCls('ok'), 'Reactivar', '¿Reactivar el acceso de este usuario?', 'post', { cliente_id: r.cliente_id }));
                } else if (r.bloqueo_tipo === 'P') {
                    appendAction(act.stack, statusPill('banned', 'Sin acciones'));
                } else if (blockUrl) {
                    appendAction(act.stack, linkBtn(
                        blockUrl,
                        actionCls('neutral'),
                        'Baja normal',
                        'CONDICIONES DE BAJA NORMAL: el usuario perderá el acceso inmediatamente y se cerrará su sesión si la tiene abierta. Su plan activo se cancelará y no conservará ninguna suscripción. Para volver, deberá crear un ticket de reactivación y acudir a recepción, donde se le facilitará un código de 6 dígitos. Al reactivarse no tendrá ningún plan activo. Lee estas condiciones al cliente antes de confirmar.',
                        'post',
                        { cliente_id: r.cliente_id, tipo: 'T' }
                    ));
                    appendAction(act.stack, linkBtn(blockUrl, actionCls('danger'), 'Banear', '¿Dar de baja permanentemente este usuario? Se cancelará también su plan activo.', 'post', { cliente_id: r.cliente_id, tipo: 'P' }));
                }
                tr.appendChild(act.td);
                frag.appendChild(tr);
            });

            return frag;
        }

        if (kind === 'monitores') {
            var pEdit = root.getAttribute('data-url-edit-prefix') || '';
            var pDel = root.getAttribute('data-url-del-prefix') || '';
            rows.forEach(function (r) {
                var tr = document.createElement('tr');
                textCell(tr, r.monitor_id);
                textCell(tr, r.DNI);
                textCell(tr, r.nombre);
                textCell(tr, [r.apellido1, r.apellido2].filter(Boolean).join(' '));
                textCell(tr, r.email);
                textCell(tr, r.telefono);
                textCell(tr, r.especialidad);
                textCell(tr, r.disponibilidad);
                var actMon = actionsCell();
                appendAction(actMon.stack, linkBtn(pEdit + encodeURIComponent(r.monitor_id), actionCls('edit'), 'Editar', ''));
                appendAction(
                    actMon.stack,
                    linkBtn(
                        pDel + encodeURIComponent(r.monitor_id),
                        actionCls('danger'),
                        'Eliminar',
                        '¿Eliminar este monitor?',
                        'post'
                    )
                );
                tr.appendChild(actMon.td);
                frag.appendChild(tr);
            });

            return frag;
        }

        if (kind === 'subscripciones') {
            var baseEdit = root.getAttribute('data-url-edit-base') || '';
            var deleteUrl = root.getAttribute('data-url-delete') || '';
            rows.forEach(function (r) {
                var tr = document.createElement('tr');
                textCell(tr, r.id);
                textCell(tr, r.nombre);
                textCell(tr, r.precio + ' €');
                textCell(tr, r.duracion + ' meses');
                textCell(tr, r.numero_clases == null ? '0' : r.numero_clases);
                textCell(tr, r.fisio === 'S' ? 'Sí' : 'No');
                textCell(tr, r.en_oferta == 1 ? ((r.oferta_motivo || 'Oferta') + (r.oferta_fin ? ' · hasta ' + String(r.oferta_fin).slice(0, 16) : '')) : '—');
                textCell(tr, r.estado === 'A' ? 'Activa' : 'Retirada');
                var actSub = actionsCell();
                var editHref = baseEdit + '?id=' + encodeURIComponent(r.id);
                appendAction(actSub.stack, linkBtn(editHref, actionCls('edit'), 'Editar', ''));
                if (deleteUrl && r.estado === 'A') {
                    appendAction(
                        actSub.stack,
                        linkBtn(
                            deleteUrl,
                            actionCls('danger'),
                            'Retirar',
                            '¿Retirar esta suscripción del catálogo? Los clientes que ya la tienen conservarán su vigencia.',
                            'post',
                            { id: r.id }
                        )
                    );
                }
                tr.appendChild(actSub.td);
                frag.appendChild(tr);
            });

            return frag;
        }

        if (kind === 'actividades') {
            var pe = root.getAttribute('data-url-edit-prefix') || '';
            var pd = root.getAttribute('data-url-del-prefix') || '';
            rows.forEach(function (r) {
                var tr = document.createElement('tr');
                var dia = DIAS[r.dia_semana] || r.dia_semana || '';
                textCell(tr, r.id);
                textCell(tr, r.nombre);
                textCell(tr, truncate(r.descripcion, 120));
                textCell(tr, r.duracion + ' min');
                textCell(tr, dia);
                textCell(tr, timeFromDt(r.fecha_inicio));
                textCell(tr, timeFromDt(r.fecha_fin));
                textCell(tr, r.sala_nombre || '—');
                textCell(tr, r.monitor_nombre || '—');
                var actAct = actionsCell();
                appendAction(actAct.stack, linkBtn(pe + encodeURIComponent(r.id), actionCls('edit'), 'Editar', ''));
                appendAction(
                    actAct.stack,
                    linkBtn(
                        pd + encodeURIComponent(r.id),
                        actionCls('danger'),
                        'Eliminar',
                        '¿Seguro que quieres eliminar esta actividad?',
                        'post'
                    )
                );
                tr.appendChild(actAct.td);
                frag.appendChild(tr);
            });

            return frag;
        }

        if (kind === 'fisioterapeutas') {
            var pfE = root.getAttribute('data-url-edit-prefix') || '';
            var pfD = root.getAttribute('data-url-del-prefix') || '';
            rows.forEach(function (r) {
                var tr = document.createElement('tr');
                textCell(tr, r.id);
                textCell(tr, r.nombre);
                textCell(tr, r.especialidad || '—');
                var actFis = actionsCell();
                appendAction(actFis.stack, linkBtn(pfE + encodeURIComponent(r.id), actionCls('edit'), 'Editar', ''));
                appendAction(
                    actFis.stack,
                    linkBtn(
                        pfD + encodeURIComponent(r.id),
                        actionCls('danger'),
                        'Eliminar',
                        '¿Eliminar este fisioterapeuta? No podrá tener citas asociadas.',
                        'post'
                    )
                );
                tr.appendChild(actFis.td);
                frag.appendChild(tr);
            });

            return frag;
        }

        if (kind === 'feedback') {
            var pfX = root.getAttribute('data-url-del-prefix') || '';
            var pfR = root.getAttribute('data-url-responder-prefix') || '';
            rows.forEach(function (r) {
                var tr = document.createElement('tr');
                textCell(tr, r.fecha_creacion);
                textCell(tr, r.nombre);
                textCell(tr, r.email);
                textCell(tr, r.asunto);
                var tdM = document.createElement('td');
                tdM.style.maxWidth = '320px';
                tdM.style.whiteSpace = 'pre-wrap';
                tdM.textContent = r.mensaje || '';

                var actFb = actionsCell();
                if (pfR) {
                    appendAction(
                        actFb.stack,
                        linkBtn(pfR + encodeURIComponent(r.id), actionCls('reply'), 'Responder', '', 'get')
                    );
                }
                appendAction(
                    actFb.stack,
                    linkBtn(pfX + encodeURIComponent(r.id), actionCls('danger'), 'Eliminar', '¿Eliminar este mensaje?', 'post')
                );
                tr.appendChild(tdM);
                tr.appendChild(actFb.td);
                frag.appendChild(tr);
            });

            return frag;
        }

        if (kind === 'solicitudes') {
            var est = (root.getAttribute('data-solicitud-estado') || 'P').toUpperCase();
            var aprUrl = root.getAttribute('data-aprobar-url') || '';
            rows.forEach(function (r) {
                var tr = document.createElement('tr');
                textCell(tr, r.id);
                textCell(tr, nombreMonitorSolicitud(r));
                textCell(tr, r.monitor_email || '—');
                textCell(tr, r.tipo || '—');
                textCell(tr, r.fecha_creacion || '—');

                var tdDesc = document.createElement('td');
                tdDesc.style.maxWidth = '280px';
                tdDesc.style.whiteSpace = 'pre-wrap';
                tdDesc.textContent = truncate(r.descripcion || '—', 220);

                tr.appendChild(tdDesc);

                textCell(tr, r.fecha_revision || '—');

                var tdEst = document.createElement('td');
                tdEst.className = 'gp-status-cell';
                var se = String(r.estado || est || 'P').toUpperCase();
                if (se === 'A') {
                    tdEst.appendChild(statusPill('ok', SolicitudEstadoMeta(se).label));
                } else if (se === 'R') {
                    tdEst.appendChild(statusPill('reject', SolicitudEstadoMeta(se).label));
                } else {
                    tdEst.appendChild(statusPill('pending', SolicitudEstadoMeta(se).label));
                }
                tr.appendChild(tdEst);

                var actSol = actionsCell();
                if (est === 'P') {
                    var btnA = document.createElement('button');
                    btnA.type = 'button';
                    btnA.className = actionCls('ok');
                    btnA.textContent = 'Aprobar';
                    btnA.addEventListener('click', function () {
                        submitPost(aprUrl, { id: String(r.id), estado: 'A' });
                    });

                    var btnR = document.createElement('button');
                    btnR.type = 'button';
                    btnR.className = actionCls('danger');
                    btnR.textContent = 'Rechazar';
                    btnR.addEventListener('click', function () {
                        if (typeof window.gpConfirm === 'function') {
                            window.gpConfirm({
                                title: 'Rechazar solicitud',
                                body: '¿Rechazar esta solicitud del monitor?',
                                danger: true,
                                okLabel: 'Rechazar',
                                onConfirm: function () {
                                    submitPost(aprUrl, { id: String(r.id), estado: 'R' });
                                },
                            });

                            return;
                        }
                        if (window.confirm('¿Rechazar esta solicitud del monitor?')) {
                            submitPost(aprUrl, { id: String(r.id), estado: 'R' });
                        }
                    });

                    appendAction(actSol.stack, btnA);
                    appendAction(actSol.stack, btnR);
                } else {
                    var dash = document.createElement('span');
                    dash.className = 'gp-actions-empty';
                    dash.textContent = '—';
                    appendAction(actSol.stack, dash);
                }
                tr.appendChild(actSol.td);
                frag.appendChild(tr);
            });

            return frag;
        }

        return frag;
    }

    function SolicitudEstadoMeta(e) {
        switch (String(e || '').toUpperCase()) {
            case 'P':
                return { label: 'Pendiente' };

            case 'A':
                return { label: 'Aprobada' };

            case 'R':
                return { label: 'Rechazada' };

            default:
                return { label: String(e || '') };
        }
    }

    function renderPagination(root, pagerEl, meta, goPage) {
        pagerEl.innerHTML = '';

        var page = meta.page || 1;
        var tp = meta.total_pages || 1;

        var nav = document.createElement('nav');
        nav.setAttribute('aria-label', 'Paginación');

        var ul = document.createElement('ul');
        ul.className = 'pagination pagination-sm mb-0 flex-wrap';

        function liDisabled(label) {
            var li = document.createElement('li');
            li.className = 'page-item disabled';

            var sp = document.createElement('span');
            sp.className = 'page-link';
            sp.textContent = label;
            li.appendChild(sp);

            return li;
        }

        function liLink(p, label, active) {
            var li = document.createElement('li');
            li.className = 'page-item' + (active ? ' active' : '');

            var a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.textContent = label;
            a.addEventListener('click', function (e) {
                e.preventDefault();
                if (!active) {
                    goPage(p);
                }
            });

            li.appendChild(a);

            return li;
        }

        if (page <= 1) {
            ul.appendChild(liDisabled('Anterior'));
        } else {
            ul.appendChild(liLink(page - 1, 'Anterior', false));
        }

        ul.appendChild(liLink(page, String(page), true));

        if (page >= tp) {
            ul.appendChild(liDisabled('Siguiente'));
        } else {
            ul.appendChild(liLink(page + 1, 'Siguiente', false));
        }

        nav.appendChild(ul);
        pagerEl.appendChild(nav);
    }

    function mount(root) {
        var kind = root.getAttribute('data-gp-admin-grid');
        var endpoint = root.getAttribute('data-endpoint');
        if (!kind || !endpoint) {
            return;
        }

        var form = root.querySelector('[data-grid-filters]');
        var tbody = root.querySelector('[data-grid-body]');
        var pager = root.querySelector('[data-grid-pagination]');
        var status = root.querySelector('[data-grid-status]');
        var selPer = root.querySelector('[data-grid-per-page]');

        if (!form || !tbody || !pager) {
            return;
        }

        var currentPage = 1;
        var loading = false;

        function getPerPage() {
            return selPer ? parseInt(selPer.value, 10) || 10 : 10;
        }

        function buildQueryString(page) {
            var p = new URLSearchParams();
            var fd = new FormData(form);
            fd.forEach(function (v, k) {
                if (String(v).trim() !== '') {
                    p.set(k, String(v));
                }
            });
            p.set('page', String(page));
            p.set('per_page', String(getPerPage()));

            if (kind === 'solicitudes') {
                var est = root.getAttribute('data-solicitud-estado') || 'P';
                p.set('estado', est);
            }

            return p.toString();
        }

        function load(page) {
            if (loading) {
                return;
            }
            loading = true;
            currentPage = page;
            if (status) {
                status.textContent = 'Cargando…';
            }

            var url = endpoint + (endpoint.indexOf('?') >= 0 ? '&' : '?') + buildQueryString(page);

            fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' })
                .then(function (r) {
                    return r.json();
                })
                .then(function (data) {
                    loading = false;
                    if (!data || !data.ok) {
                        tbody.innerHTML = '';
                        var tr = document.createElement('tr');
                        var td = document.createElement('td');
                        td.colSpan = parseInt(root.getAttribute('data-colspan') || '1', 10);
                        td.className = 'text-danger';
                        td.textContent = (data && data.error) || 'Error al cargar datos.';
                        tr.appendChild(td);
                        tbody.appendChild(tr);
                        if (status) {
                            status.textContent = '';
                        }

                        return;
                    }

                    tbody.innerHTML = '';
                    tbody.appendChild(buildRows(kind, data.rows, root));
                    renderPagination(root, pager, data, function (np) {
                        load(np);
                    });

                    if (status) {
                        var total = data.total != null ? data.total : 0;
                        status.textContent = 'Mostrando página ' + (data.page || 1) + ' de ' + (data.total_pages || 1) + ' · ' + total + ' registros';
                    }
                })
                .catch(function () {
                    loading = false;
                    tbody.innerHTML = '';
                    var tr = document.createElement('tr');
                    var td = document.createElement('td');
                    td.colSpan = parseInt(root.getAttribute('data-colspan') || '1', 10);
                    td.className = 'text-danger';
                    td.textContent = 'Error de red. Revisa la conexión e inténtalo de nuevo.';
                    tr.appendChild(td);
                    tbody.appendChild(tr);

                    if (status) {
                        status.textContent = '';
                    }
                });
        }

        var debounced = debounce(function () {
            load(1);
        }, 400);

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            load(1);
        });

        form.addEventListener('input', function () {
            debounced();
        });

        form.addEventListener('change', function (e) {
            if (e.target && e.target.matches('select')) {
                debounced();
            }
        });

        if (selPer) {
            selPer.addEventListener('change', function () {
                load(1);
            });
        }

        root.querySelectorAll('[data-grid-reset]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                form.reset();
                load(1);
            });
        });

        load(1);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-gp-admin-grid]').forEach(function (el) {
            mount(el);
        });
    });
})();
