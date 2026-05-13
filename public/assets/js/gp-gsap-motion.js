/**
 * Spartum — animación ligera solo en paneles inicio (bento).
 */
(function () {
    'use strict';

    if (typeof window.gsap === 'undefined') {
        return;
    }

    function prefersReducedMotion() {
        try {
            return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        } catch (_) {
            return false;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (prefersReducedMotion()) {
            return;
        }
        var dash = document.querySelector('.gp-dash .gp-bento');
        if (!dash) {
            return;
        }
        var tiles = dash.querySelectorAll('.gp-bento-tile');
        if (!tiles.length) {
            return;
        }
        gsap.from(tiles, {
            opacity: 0,
            y: 14,
            duration: 0.4,
            stagger: 0.04,
            ease: 'power2.out',
            clearProps: 'transform',
        });
    });
})();
