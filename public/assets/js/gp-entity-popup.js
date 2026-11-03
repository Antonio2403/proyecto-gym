/**
 * Editar (y responder) se abre en un popup. Eliminar confirma y actualiza el listado
 * sin cambiar de página.
 */
(function () {
    'use strict';

    var UI = typeof SpartumUI !== 'undefined' ? SpartumUI : null;

    function modalEl() {
        return document.getElementById('gpEntityModal');
    }

    function modalApi() {
        var el = modalEl();
        if (!el || !UI || !UI.Modal) {
            return null;
        }

        return UI.Modal.getOrCreateInstance(el);
    }

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');

        return meta ? String(meta.getAttribute('content') || '') : '';
    }

    function ensureCsrf(form) {
        var token = csrfToken();
        if (!token) {
            return;
        }
        var inp = form.querySelector('input[name="csrf_token"]');
        if (!inp) {
            inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'csrf_token';
            form.appendChild(inp);
        }
        inp.value = token;
    }

    function bodyEl() {
        var modal = modalEl();

        return modal ? modal.querySelector('[data-gp-entity-body]') : null;
    }

    function showModal() {
        var api = modalApi();
        if (api) {
            api.show();
        }
    }

    function hideModal() {
        var api = modalApi();
        if (api) {
            api.hide();
        }
    }

    function setStatus(message) {
        var body = bodyEl();
        if (!body) {
            return;
        }
        body.innerHTML = '';
        var p = document.createElement('p');
        p.className = 'gp-entity-modal__status';
        p.textContent = message;
        body.appendChild(p);
        showModal();
    }

    function placeFlash(doc) {
        var incoming = doc.querySelector('[data-gp-flash-banner]');
        var current = document.querySelector('[data-gp-flash-banner]');
        if (current) {
            current.remove();
        }
        if (!incoming) {
            return;
        }
        var host = document.querySelector('.gp-admin-content') || document.querySelector('.gp-admin-page');
        if (!host) {
            return;
        }
        var node = document.importNode(incoming, true);
        host.insertBefore(node, host.firstChild);
        var close = node.querySelector('[data-gp-flash-dismiss]');
        if (close) {
            close.addEventListener('click', function () {
                node.remove();
            });
        }
        window.setTimeout(function () {
            if (node.isConnected) {
                node.remove();
            }
        }, 9000);
    }

    function refreshList(finalUrl) {
        if (document.querySelector('[data-gp-admin-grid]')) {
            document.dispatchEvent(new CustomEvent('gp-admin-refresh'));
            if (finalUrl) {
                try {
                    var next = new URL(finalUrl, window.location.href);
                    if (next.pathname === window.location.pathname && next.search !== window.location.search) {
                        history.replaceState(null, '', next.pathname + next.search);
                    }
                } catch (e) {
                }
            }

            return;
        }
        window.location.href = finalUrl || window.location.href;
    }

    function wireForm(form) {
        form.removeAttribute('data-gp-confirm');
        ensureCsrf(form);
        if (typeof window.gpBindDynamicForm === 'function') {
            window.gpBindDynamicForm(form.parentElement || form);
        }
        form.querySelectorAll('a.btn').forEach(function (link) {
            link.addEventListener('click', function (ev) {
                ev.preventDefault();
                hideModal();
            });
        });
        form.addEventListener('submit', function (ev) {
            if (ev.defaultPrevented) {
                return;
            }
            ev.preventDefault();
            ensureCsrf(form);
            var submit = form.querySelector('[type="submit"]');
            if (submit) {
                submit.disabled = true;
            }
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin',
                headers: { Accept: 'text/html' },
            }).then(function (r) {
                return r.text().then(function (html) {
                    return { url: r.url, html: html };
                });
            }).then(function (res) {
                window.gpApplyAdminResult(res, { allowForm: true });
            }).catch(function () {
                setStatus('No se pudo guardar. Revisa la conexión e inténtalo de nuevo.');
            });
        });
    }

    function fillFromDocument(doc) {
        var body = bodyEl();
        var panel = doc.querySelector('.gp-form-panel');
        if (!body || !panel) {
            setStatus('No se pudo abrir el formulario.');

            return;
        }
        body.innerHTML = '';
        var flash = doc.querySelector('[data-gp-flash-banner]');
        if (flash) {
            body.appendChild(document.importNode(flash, true));
        }
        var node = document.importNode(panel, true);
        var back = node.querySelector('.gp-form-panel__back');
        if (back) {
            back.remove();
        }
        var head = node.querySelector('.gp-form-panel__head') || node;
        var close = document.createElement('button');
        close.type = 'button';
        close.className = 'btn-close gp-entity-close';
        close.setAttribute('data-gp-dismiss', 'modal');
        close.setAttribute('aria-label', 'Cerrar');
        head.appendChild(close);
        body.appendChild(node);
        var form = node.querySelector('form');
        if (form) {
            wireForm(form);
            var first = form.querySelector('input:not([type="hidden"]), select, textarea');
            if (first) {
                window.setTimeout(function () {
                    first.focus();
                }, 40);
            }
        }
        showModal();
    }

    function openEdit(url) {
        setStatus('Cargando…');
        fetch(url, {
            credentials: 'same-origin',
            headers: { Accept: 'text/html' },
        }).then(function (r) {
            return r.text().then(function (html) {
                return { url: r.url, html: html };
            });
        }).then(function (res) {
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            if (!doc.querySelector('.gp-form-panel')) {
                setStatus('No se pudo abrir el formulario.');

                return;
            }
            fillFromDocument(doc);
        }).catch(function () {
            setStatus('No se pudo abrir el formulario. Revisa la conexión e inténtalo de nuevo.');
        });
    }

    window.gpApplyAdminResult = function (res, opts) {
        opts = opts || {};
        var doc = new DOMParser().parseFromString(res.html || '', 'text/html');
        var panel = doc.querySelector('.gp-form-panel');
        var finalUrl = res.url || window.location.href;
        var samePage = false;
        try {
            samePage = new URL(finalUrl, window.location.href).pathname === window.location.pathname;
        } catch (e) {
            samePage = false;
        }
        if (opts.allowForm && panel && !samePage) {
            fillFromDocument(doc);

            return;
        }
        hideModal();
        placeFlash(doc);
        refreshList(finalUrl);
    };

    window.gpAjaxSubmit = function (form) {
        if (!form || !form.action) {
            return;
        }
        ensureCsrf(form);
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            credentials: 'same-origin',
            headers: { Accept: 'text/html' },
        }).then(function (r) {
            return r.text().then(function (html) {
                return { url: r.url, html: html };
            });
        }).then(function (res) {
            window.gpApplyAdminResult(res);
        }).catch(function () {
            window.location.href = form.action;
        });
    };

    document.addEventListener('click', function (ev) {
        if (!document.body.classList.contains('gp-admin-body-root')) {
            return;
        }
        if (ev.button !== 0 || ev.metaKey || ev.ctrlKey || ev.shiftKey || ev.altKey) {
            return;
        }
        var link = ev.target.closest('a.gp-btn-action--edit, a.gp-btn-action--reply');
        if (!link || !link.href || link.target === '_blank') {
            return;
        }
        ev.preventDefault();
        openEdit(link.href);
    }, true);
})();
