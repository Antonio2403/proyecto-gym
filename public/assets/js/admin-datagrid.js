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

    function linkBtn(href, cls, label, confirmMsg) {
        var a = document.createElement('a');
        a.href = href;
        a.className = cls;
        a.textContent = label;
        if (confirmMsg) {
            a.addEventListener('click', function (e) {
                e.preventDefault();
                if (typeof window.gpConfirm === 'function') {
                    window.gpConfirm({
                        title: 'Confirmar eliminación',
                        body: confirmMsg,
                        danger: true,
                        okLabel: 'Eliminar',
                        onConfirm: function () {
                            window.location.href = href;
                        },
                    });

                    return;
                }
                if (window.confirm(confirmMsg)) {
                    window.location.href = href;
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
            rows.forEach(function (r) {
                var tr = document.createElement('tr');
                textCell(tr, r.cliente_id);
                textCell(tr, r.DNI);
                textCell(tr, nombreCliente(r));
                textCell(tr, r.email);
                textCell(tr, r.telefono);
                textCell(tr, r.metodo_pago || '—');
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
                var tdA = document.createElement('td');
                tdA.className = 'text-nowrap';
                tdA.appendChild(linkBtn(pEdit + encodeURIComponent(r.monitor_id), 'btn btn-warning btn-sm', 'Editar', ''));
                tdA.appendChild(document.createTextNode(' '));
                tdA.appendChild(
                    linkBtn(
                        pDel + encodeURIComponent(r.monitor_id),
                        'btn btn-danger btn-sm',
                        'Eliminar',
                        '¿Eliminar este monitor?'
                    )
                );
                tr.appendChild(tdA);
                frag.appendChild(tr);
            });

            return frag;
        }

        if (kind === 'subscripciones') {
            var baseEdit = root.getAttribute('data-url-edit-base') || '';
            rows.forEach(function (r) {
                var tr = document.createElement('tr');
                textCell(tr, r.id);
                textCell(tr, r.nombre);
                textCell(tr, r.precio + ' €');
                textCell(tr, r.duracion + ' meses');
                var tdA = document.createElement('td');
                var editHref = baseEdit + '?id=' + encodeURIComponent(r.id);
                tdA.appendChild(linkBtn(editHref, 'btn btn-sm btn-warning', 'Editar', ''));

                tr.appendChild(tdA);
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
                var tdA = document.createElement('td');
                tdA.className = 'text-nowrap';
                tdA.appendChild(linkBtn(pe + encodeURIComponent(r.id), 'btn btn-warning btn-sm', 'Editar', ''));
                tdA.appendChild(document.createTextNode(' '));
                tdA.appendChild(
                    linkBtn(
                        pd + encodeURIComponent(r.id),
                        'btn btn-danger btn-sm',
                        'Eliminar',
                        '¿Seguro que quieres eliminar esta actividad?'
                    )
                );

                tr.appendChild(tdA);
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
                var tdA = document.createElement('td');
                tdA.className = 'text-nowrap';
                tdA.appendChild(linkBtn(pfE + encodeURIComponent(r.id), 'btn btn-sm btn-outline-primary', 'Editar', ''));
                tdA.appendChild(document.createTextNode(' '));
                tdA.appendChild(
                    linkBtn(
                        pfD + encodeURIComponent(r.id),
                        'btn btn-sm btn-outline-danger',
                        'Eliminar',
                        '¿Eliminar este fisioterapeuta? No podrá tener citas asociadas.'
                    )
                );

                tr.appendChild(tdA);
                frag.appendChild(tr);
            });

            return frag;
        }

        if (kind === 'feedback') {
            var pfX = root.getAttribute('data-url-del-prefix') || '';
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

                var tdA = document.createElement('td');
                tdA.appendChild(
                    linkBtn(pfX + encodeURIComponent(r.id), 'btn btn-sm btn-danger', 'Eliminar', '¿Eliminar este mensaje?')
                );
                tr.appendChild(tdM);
                tr.appendChild(tdA);
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

                var se = String(r.estado || est || 'P').toUpperCase();

                var tdEst = document.createElement('td');
                var badge = document.createElement('span');
                badge.className =
                    se === 'A' ? 'badge text-bg-success' : se === 'R' ? 'badge text-bg-danger' : 'badge text-bg-warning text-dark';
                badge.textContent = SolicitudEstadoMeta(se).label;

                tdEst.appendChild(badge);

                tr.appendChild(tdEst);

                var tdAct = document.createElement('td');
                tdAct.className = 'text-nowrap';

                if (est === 'P') {
                    var form = document.createElement('form');
                    form.method = 'post';
                    form.action = aprUrl;
                    form.className = 'd-inline-flex flex-column gap-1 align-items-start';

                    var hid = document.createElement('input');
                    hid.type = 'hidden';
                    hid.name = 'id';
                    hid.value = String(r.id);
                    form.appendChild(hid);

                    var btnA = document.createElement('button');
                    btnA.type = 'submit';
                    btnA.name = 'estado';
                    btnA.value = 'A';
                    btnA.className = 'btn btn-sm btn-success';
                    btnA.textContent = 'Aprobar';

                    var btnR = document.createElement('button');
                    btnR.type = 'submit';
                    btnR.name = 'estado';
                    btnR.value = 'R';
                    btnR.className = 'btn btn-sm btn-danger';
                    btnR.textContent = 'Rechazar';

                    form.appendChild(btnA);
                    form.appendChild(btnR);

                    tdAct.appendChild(form);
                } else {
                    tdAct.textContent = '—';
                }

                tr.appendChild(tdAct);
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
