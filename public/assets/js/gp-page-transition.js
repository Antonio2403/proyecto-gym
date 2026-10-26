/**
 * La cortina de navegación ya no se usa.
 * Este archivo solo despeja una marca antigua para que una página en caché no se quede tapada.
 */
(function () {
    'use strict';

    try {
        sessionStorage.removeItem('gpPageTransition');
    } catch (e) {}

    document.documentElement.classList.remove('gp-nav-curtain-hold', 'gp-transition-lock');

    var curtain = document.getElementById('gp-page-curtain');
    if (curtain) {
        curtain.remove();
    }
})();
