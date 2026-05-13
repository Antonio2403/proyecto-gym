/**
 * Banners flash bajo el header: cierre manual y auto-ocultado.
 */
(function () {
    'use strict';

    function dismissBanner(banner) {
        if (!banner || banner.classList.contains('gp-flash-banner--hide')) {
            return;
        }
        banner.classList.add('gp-flash-banner--hide');
        window.setTimeout(function () {
            banner.remove();
        }, 320);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-gp-flash-banner]').forEach(function (banner) {
            window.setTimeout(function () {
                dismissBanner(banner);
            }, 9000);

            var btn = banner.querySelector('[data-gp-flash-dismiss]');
            if (btn) {
                btn.addEventListener('click', function () {
                    dismissBanner(banner);
                });
            }
        });
    });
})();
