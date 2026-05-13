/**
 * Modal Bootstrap genérico para confirmar enlaces y envíos de formulario.
 *
 * Formularios: añade data-gp-confirm (+ data opcionales) al <form>. El primer envío
 * abre el modal; tras confirmar se reenvía (submit programático sin bucle infinito).
 *
 * Enlaces: <a href="..." data-gp-confirm>...</a>
 */
(function () {
    'use strict';

    var BP = typeof bootstrap !== 'undefined' ? bootstrap : null;

    function getModalEl() {
        return document.getElementById('gpGlobalConfirmModal');
    }

    function getInstance() {
        var el = getModalEl();
        if (!el || !BP || !BP.Modal) {
            return null;
        }

        return BP.Modal.getOrCreateInstance(el);
    }

    /**
     * @param {object} opts
     * @param {string} [opts.title]
     * @param {string} [opts.body]
     * @param {string} [opts.bodyHtml]
     * @param {string} [opts.okLabel]
     * @param {boolean} [opts.danger]
     * @param {function()} opts.onConfirm
     */
    function gpConfirm(opts) {
        opts = opts || {};
        var el = getModalEl();
        var inst = getInstance();
        if (!el || !inst) {
            if (opts.onConfirm) {
                opts.onConfirm();
            }

            return;
        }

        var titleEl = el.querySelector('[data-gp-confirm-modal-title]');
        var bodyEl = el.querySelector('[data-gp-confirm-modal-body]');
        var okBtn = el.querySelector('[data-gp-confirm-modal-ok]');
        if (!titleEl || !bodyEl || !okBtn) {
            if (opts.onConfirm) {
                opts.onConfirm();
            }

            return;
        }

        titleEl.textContent = opts.title || 'Confirmar acción';
        if (opts.bodyHtml) {
            bodyEl.innerHTML = opts.bodyHtml;
        } else {
            bodyEl.textContent = opts.body || '¿Continuar con esta acción?';
        }
        okBtn.textContent = opts.okLabel || 'Sí, continuar';

        okBtn.className = 'btn ' + (opts.danger ? 'btn-danger' : 'btn-primary');

        var cloned = okBtn.cloneNode(true);
        okBtn.parentNode.replaceChild(cloned, okBtn);
        cloned.addEventListener('click', function oneConfirm() {
            inst.hide();
            if (opts.onConfirm) {
                opts.onConfirm();
            }
        });

        inst.show();
    }

    window.gpConfirm = gpConfirm;

    /**
     * @param {HTMLElement} el
     * @param {string} key camelCase tras data-
     */
    function readDs(el, key, fallback) {
        var v = el.dataset[key];
        if (v !== undefined && v !== '') {
            return v;
        }

        return fallback;
    }

    function submitFormWithSubmitter(form, sub) {
        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit(sub || undefined);

            return;
        }
        if (sub && sub.getAttribute && sub.name) {
            var h = document.createElement('input');
            h.type = 'hidden';
            h.name = String(sub.name);
            h.value = String(sub.value || '');
            form.appendChild(h);
            form.submit();
            form.removeChild(h);

            return;
        }
        form.submit();
    }

    function bindForms() {
        document.addEventListener(
            'submit',
            function (ev) {
                var form = ev.target;
                if (!form || form.nodeName !== 'FORM' || !form.hasAttribute('data-gp-confirm')) {
                    return;
                }
                if (form.__gpConfirmBypass) {
                    form.__gpConfirmBypass = false;

                    return;
                }

                ev.preventDefault();

                gpConfirm({
                    title: readDs(form, 'gpConfirmTitle', 'Confirmar'),
                    body: readDs(form, 'gpConfirmBody', '¿Enviar este formulario?'),
                    bodyHtml: readDs(form, 'gpConfirmBodyHtml', ''),
                    okLabel: readDs(form, 'gpConfirmOk', 'Sí'),
                    danger: form.getAttribute('data-gp-danger') === 'true',
                    onConfirm: function () {
                        form.__gpConfirmBypass = true;
                        submitFormWithSubmitter(form, ev.submitter);
                    },
                });
            },
            true
        );
    }

    function bindLinks() {
        document.addEventListener(
            'click',
            function (ev) {
                var a = ev.target.closest('a[data-gp-confirm]');
                if (!a || !a.href) {
                    return;
                }
                ev.preventDefault();

                gpConfirm({
                    title: readDs(a, 'gpConfirmTitle', 'Confirmar'),
                    body: readDs(a, 'gpConfirmBody', '¿Seguir con esta acción?'),
                    bodyHtml: readDs(a, 'gpConfirmBodyHtml', ''),
                    okLabel: readDs(a, 'gpConfirmOk', 'Sí'),
                    danger: a.getAttribute('data-gp-danger') === 'true',
                    onConfirm: function () {
                        window.location.href = a.href;
                    },
                });
            },
            false
        );
    }

    function bindDelegatedSolicitudForms() {
        document.addEventListener(
            'submit',
            function (ev) {
                var form = ev.target;
                if (!form || form.nodeName !== 'FORM') {
                    return;
                }

                var root = form.closest('[data-gp-admin-grid][data-admin-solicitudes-pendientes="1"]');
                if (!root) {
                    return;
                }

                var sub = ev.submitter;

                // Solo las acciones Aprueba/Rechaza
                if (!sub || String(sub.name) !== 'estado') {
                    return;
                }

                if (form.__gpSolicBypass) {
                    delete form.__gpSolicBypass;

                    return;
                }

                ev.preventDefault();

                var val = String(sub.value || '').toUpperCase();

                gpConfirm({
                    title: val === 'A' ? 'Aprobar solicitud' : 'Rechazar solicitud',
                    body:
                        val === 'A'
                            ? '¿Aprobar esta solicitud del monitor?'
                            : '¿Rechazar esta solicitud del monitor?',
                    okLabel: val === 'A' ? 'Sí, aprobar' : 'Sí, rechazar',
                    danger: val === 'R',
                    onConfirm: function () {
                        form.__gpSolicBypass = true;
                        submitFormWithSubmitter(form, sub);
                    },
                });
            },
            true
        );
    }

    bindForms();
    bindLinks();
    bindDelegatedSolicitudForms();
})();
