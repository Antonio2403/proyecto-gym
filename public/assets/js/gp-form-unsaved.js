/**
 * Aviso al salir de un formulario con cambios sin guardar.
 */
(function () {
    'use strict';

    function isDirty(form) {
        return form && form.getAttribute('data-gp-dirty') === '1';
    }

    function markDirty(form) {
        if (form) {
            form.setAttribute('data-gp-dirty', '1');
        }
    }

    function clearDirty(form) {
        if (form) {
            form.removeAttribute('data-gp-dirty');
        }
    }

    function confirmLeave(href) {
        if (typeof window.gpConfirm === 'function') {
            window.gpConfirm({
                title: 'Salir sin guardar',
                body: 'Tienes cambios sin guardar. Si sales ahora, no se aplicarán.',
                okLabel: 'Salir sin guardar',
                danger: true,
                onConfirm: function () {
                    window.location.href = href;
                },
            });

            return;
        }
        if (window.confirm('Tienes cambios sin guardar. ¿Salir sin guardar?')) {
            window.location.href = href;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[data-gp-unsaved-guard]').forEach(function (form) {
            form.addEventListener('input', function () {
                markDirty(form);
            });
            form.addEventListener('change', function () {
                markDirty(form);
            });
            form.addEventListener('submit', function () {
                clearDirty(form);
            });

            var back = form.closest('.gp-form-panel') && form.closest('.gp-form-panel').querySelector('.gp-form-panel__back');
            if (back && back.href) {
                back.addEventListener('click', function (e) {
                    if (!isDirty(form)) {
                        return;
                    }
                    e.preventDefault();
                    confirmLeave(back.href);
                });
            }

            form.querySelectorAll('[data-gp-unsaved-cancel]').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    if (!isDirty(form) || !link.href) {
                        return;
                    }
                    e.preventDefault();
                    confirmLeave(link.href);
                });
            });
        });

        window.addEventListener('beforeunload', function (e) {
            var dirty = document.querySelector('form[data-gp-unsaved-guard][data-gp-dirty="1"]');
            if (!dirty) {
                return;
            }
            e.preventDefault();
            e.returnValue = '';
        });
    });
})();
