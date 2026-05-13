(function () {
    'use strict';

    var scriptEl = document.currentScript;
    var sessionStatusUrl = scriptEl ? String(scriptEl.getAttribute('data-session-status-url') || '') : '';
    var loginUrl = scriptEl ? String(scriptEl.getAttribute('data-login-url') || '') : '';
    var cerrarInactividadUrl = scriptEl ? String(scriptEl.getAttribute('data-cerrar-inactividad-url') || '') : '';
    if (!loginUrl) {
        loginUrl = '/login';
    }

    function escAttr(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function readIdleSecondsFromMeta() {
        var m = document.querySelector('meta[name="gp-session-idle-seconds"]');
        if (!m) {
            return 0;
        }
        var n = parseInt(String(m.getAttribute('content') || '0'), 10);

        return n > 0 ? n : 0;
    }

    function csrfFromMeta() {
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');

        return csrfMeta ? String(csrfMeta.getAttribute('content') || '') : '';
    }

    function buildThemedModalHtml(opts) {
        var variant = opts.variant || 'neutral';
        var headerClass = 'modal-header border-secondary border-opacity-25';
        if (variant === 'danger') {
            headerClass += ' bg-danger text-white border-0';
        } else if (variant === 'warning') {
            headerClass += ' bg-warning text-dark border-0';
        } else if (variant === 'info') {
            headerClass += ' bg-info bg-opacity-25 text-white border-0';
        }
        var title = opts.title || 'Aviso';
        var body = opts.bodyHtml || '';
        var okHref = opts.okHref || loginUrl;
        var okLabel = opts.okLabel || 'Entendido';

        return (
            '<div class="modal-dialog modal-dialog-centered">' +
            '<div class="modal-content gp-modal-dark border-secondary border-opacity-25 shadow">' +
            '<div class="' + headerClass + '">' +
            '<h5 class="modal-title">' + title + '</h5>' +
            (opts.showClose
                ? '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>'
                : '') +
            '</div>' +
            '<div class="modal-body text-white-50">' + body + '</div>' +
            '<div class="modal-footer border-secondary border-opacity-25">' +
            '<a class="btn btn-primary" href="' + okHref + '">' + okLabel + '</a>' +
            '</div>' +
            '</div>' +
            '</div>'
        );
    }

    function showForcedLogoutModal(data) {
        var modalId = 'gpForcedLogoutModal';
        var existing = document.getElementById(modalId);
        if (existing) {
            existing.remove();
        }
        var wrap = document.createElement('div');
        wrap.className = 'modal fade';
        wrap.id = modalId;
        wrap.tabIndex = -1;
        wrap.setAttribute('aria-hidden', 'true');

        var kind = data && data.logout_kind ? String(data.logout_kind) : '';
        var isPermanent = kind === 'baja_permanente';
        var variant = isPermanent ? 'danger' : 'warning';
        var title = isPermanent ? 'Cuenta dada de baja' : 'Sesión cerrada';
        var msg =
            (data && data.message) ||
            'Tu cuenta ha sido dada de baja y se ha cerrado la sesión.';
        var bodyHtml = '<p class="mb-0 text-white">' + msg + '</p>';

        wrap.innerHTML = buildThemedModalHtml({
            variant: variant,
            title: title,
            bodyHtml: bodyHtml,
            okHref: loginUrl,
            okLabel: 'Ir al inicio de sesión',
            showClose: false,
        });
        document.body.appendChild(wrap);

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(wrap, { backdrop: 'static', keyboard: false }).show();
        } else {
            window.alert(msg);
        }

        window.setTimeout(function () {
            window.location.href = loginUrl;
        }, 6000);
    }

    /** Tras cerrar sesión en servidor por tiempo de inactividad (cliente). */
    function showInactivityClosedModal() {
        var overlay = document.getElementById('gpIdleOverlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'gpIdleOverlay';
            overlay.className = 'gp-idle-overlay';
            overlay.setAttribute('role', 'dialog');
            overlay.setAttribute('aria-modal', 'true');
            overlay.setAttribute('aria-labelledby', 'gpIdleOverlayTitle');
            var href = escAttr(loginUrl);
            overlay.innerHTML =
                '<div class="gp-idle-overlay__card">' +
                '<div class="gp-idle-overlay__head">' +
                '<span class="gp-idle-overlay__icon" aria-hidden="true"><i class="fas fa-clock"></i></span>' +
                '<h2 class="gp-idle-overlay__title" id="gpIdleOverlayTitle">Sesión cerrada por inactividad</h2>' +
                '</div>' +
                '<div class="gp-idle-overlay__body">' +
                '<p class="mb-0">Has superado el tiempo de inactividad configurado. Por seguridad, la sesión se ha cerrado.</p>' +
                '</div>' +
                '<div class="gp-idle-overlay__foot">' +
                '<a class="btn btn-primary w-100" href="' + href + '">Ir al inicio de sesión</a>' +
                '</div>' +
                '</div>';
            document.body.appendChild(overlay);
        }
        overlay.hidden = false;
        window.requestAnimationFrame(function () {
            overlay.classList.add('is-open');
        });
    }

    function postCerrarInactividad(onDone) {
        var token = csrfFromMeta();
        if (!cerrarInactividadUrl) {
            onDone(false);

            return;
        }
        fetch(cerrarInactividadUrl, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-Token': token,
            },
            credentials: 'same-origin',
            body: '{}',
        })
            .then(function (res) {
                return res.json().catch(function () {
                    return { ok: false };
                });
            })
            .then(function (data) {
                onDone(data && data.ok === true);
            })
            .catch(function () {
                onDone(false);
            });
    }

    function startStrictIdleLogout() {
        var ttl = readIdleSecondsFromMeta();
        if (ttl < 60 || !cerrarInactividadUrl) {
            return;
        }
        var ttlMs = ttl * 1000;
        var last = Date.now();
        var closing = false;

        function bump() {
            last = Date.now();
        }

        ['click', 'keydown', 'scroll', 'touchstart', 'mousemove'].forEach(function (ev) {
            document.addEventListener(ev, bump, { passive: true });
        });

        window.setInterval(function () {
            if (closing) {
                return;
            }
            if (Date.now() - last < ttlMs) {
                return;
            }
            closing = true;
            postCerrarInactividad(function () {
                showInactivityClosedModal();
            });
        }, 4000);
    }

    function startSessionWatcher() {
        if (!sessionStatusUrl) {
            return;
        }
        var stopped = false;
        function check() {
            if (stopped || document.hidden) {
                return;
            }
            fetch(sessionStatusUrl, {
                method: 'GET',
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            })
                .then(function (res) {
                    return res.json();
                })
                .then(function (data) {
                    if (data && data.forced_logout) {
                        stopped = true;
                        showForcedLogoutModal(data);
                    }
                })
                .catch(function () {
                });
        }
        window.setInterval(check, 5000);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var box = document.querySelector('[data-gp-cookie-consent]');
        var btn = document.querySelector('[data-gp-cookie-accept]');
        startSessionWatcher();
        startStrictIdleLogout();
        if (!box || !btn) {
            return;
        }

        function closeConsent() {
            box.classList.add('gp-cookie-consent--closing');
            window.setTimeout(function () {
                box.hidden = true;
            }, 380);
        }

        function rememberLocally() {
            var secure = window.location.protocol === 'https:' ? '; Secure' : '';
            document.cookie = 'gp_cookie_consent=accepted; Max-Age=31536000; Path=/; SameSite=Lax' + secure;
            try {
                window.localStorage.setItem('gp_cookie_consent', 'accepted');
            } catch (_) {
            }
        }

        btn.addEventListener('click', function () {
            var csrfToken = csrfFromMeta();
            btn.disabled = true;
            rememberLocally();
            closeConsent();

            fetch(String(box.getAttribute('data-cookie-url') || '/cookies/aceptar'), {
                method: 'POST',
                headers: { 'X-CSRF-Token': csrfToken },
                credentials: 'same-origin',
            }).catch(function () {
            });
        });
    });
})();
