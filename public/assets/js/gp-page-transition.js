/**
 * Transición Spartum — cortinas + logo.
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'gpPageTransition';
    var EXTRA_WAIT_MS = 350;
    var CLOSE_DUR = 0.4;
    var OPEN_DUR = 0.42;
    var LOGO_IN_DUR = 0.28;
    var LOGO_OUT_DUR = 0.3;

    var curtainEl = null;
    var topPanel = null;
    var bottomPanel = null;
    var logoEl = null;
    var navigating = false;
    var scrollLocked = false;

    function lockScroll() {
        if (scrollLocked) {
            return;
        }
        scrollLocked = true;
        var sw = window.innerWidth - document.documentElement.clientWidth;
        document.documentElement.classList.add('gp-transition-lock');
        if (sw > 0) {
            document.body.style.paddingRight = sw + 'px';
        }
    }

    function unlockScroll() {
        if (!scrollLocked) {
            return;
        }
        scrollLocked = false;
        document.documentElement.classList.remove('gp-transition-lock');
        document.body.style.paddingRight = '';
    }

    function removeBootHold() {
        document.documentElement.classList.remove('gp-nav-curtain-hold');
    }

    function prefersReducedMotion() {
        try {
            return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        } catch (_) {
            return false;
        }
    }

    function gsapRef() {
        return window.gsap;
    }

    function ensureCurtain() {
        if (curtainEl) {
            return curtainEl;
        }
        curtainEl = document.createElement('div');
        curtainEl.id = 'gp-page-curtain';
        curtainEl.setAttribute('aria-hidden', 'true');

        topPanel = document.createElement('div');
        topPanel.className = 'gp-page-curtain__top';

        bottomPanel = document.createElement('div');
        bottomPanel.className = 'gp-page-curtain__bottom';

        logoEl = document.createElement('div');
        logoEl.className = 'gp-page-curtain__logo';
        logoEl.textContent = 'Spartum';

        curtainEl.appendChild(topPanel);
        curtainEl.appendChild(bottomPanel);
        curtainEl.appendChild(logoEl);
        document.body.appendChild(curtainEl);

        topPanel = curtainEl.querySelector('.gp-page-curtain__top');
        bottomPanel = curtainEl.querySelector('.gp-page-curtain__bottom');
        logoEl = curtainEl.querySelector('.gp-page-curtain__logo');

        return curtainEl;
    }

    function setPanelsOpenInstant() {
        if (!gsapRef()) {
            return;
        }
        gsapRef().set(topPanel, { yPercent: -100, force3D: true });
        gsapRef().set(bottomPanel, { yPercent: 100, force3D: true });
        gsapRef().set(logoEl, { opacity: 0, visibility: 'hidden' });
    }

    function setPanelsClosedInstant() {
        if (!gsapRef()) {
            return;
        }
        gsapRef().set(topPanel, { yPercent: 0, force3D: true });
        gsapRef().set(bottomPanel, { yPercent: 0, force3D: true });
    }

    function activateCurtain() {
        ensureCurtain();
        curtainEl.classList.add('is-active');
        curtainEl.setAttribute('aria-hidden', 'false');
    }

    function deactivateCurtain() {
        if (!curtainEl) {
            return;
        }
        setPanelsOpenInstant();
        curtainEl.classList.remove('is-active');
        curtainEl.setAttribute('aria-hidden', 'true');
        removeBootHold();
        unlockScroll();
    }

    function mountArrivalCurtain() {
        ensureCurtain();
        lockScroll();
        activateCurtain();
        setPanelsClosedInstant();
        if (gsapRef()) {
            gsapRef().set(logoEl, { opacity: 1, visibility: 'visible' });
        } else {
            logoEl.style.opacity = '1';
        }
        requestAnimationFrame(function () {
            requestAnimationFrame(removeBootHold);
        });
    }

    function animateClosePanels(done) {
        lockScroll();
        activateCurtain();
        removeBootHold();

        if (!gsapRef() || prefersReducedMotion()) {
            setPanelsClosedInstant();
            if (typeof done === 'function') {
                done();
            }

            return;
        }

        gsapRef().killTweensOf([topPanel, bottomPanel, logoEl]);
        gsapRef().set(topPanel, { yPercent: -100, force3D: true });
        gsapRef().set(bottomPanel, { yPercent: 100, force3D: true });
        gsapRef().set(logoEl, { opacity: 0, visibility: 'hidden' });
        gsapRef()
            .timeline({ onComplete: done })
            .to(topPanel, { yPercent: 0, duration: CLOSE_DUR, ease: 'power3.inOut' }, 0)
            .to(bottomPanel, { yPercent: 0, duration: CLOSE_DUR, ease: 'power3.inOut' }, 0);
    }

    function animateOpenPanels(done) {
        removeBootHold();

        if (gsapRef()) {
            gsapRef().set(logoEl, { opacity: 0, visibility: 'hidden' });
        }

        if (!gsapRef() || prefersReducedMotion()) {
            deactivateCurtain();
            if (typeof done === 'function') {
                done();
            }

            return;
        }

        gsapRef().killTweensOf([topPanel, bottomPanel]);
        gsapRef()
            .timeline({
                onComplete: function () {
                    deactivateCurtain();
                    if (typeof done === 'function') {
                        done();
                    }
                },
            })
            .to(topPanel, { yPercent: -100, duration: OPEN_DUR, ease: 'power3.inOut' }, 0)
            .to(bottomPanel, { yPercent: 100, duration: OPEN_DUR, ease: 'power3.inOut' }, 0);
    }

    function fadeInLogo(done) {
        if (!gsapRef() || prefersReducedMotion()) {
            logoEl.style.opacity = '1';
            logoEl.style.visibility = 'visible';
            if (typeof done === 'function') {
                done();
            }

            return;
        }
        gsapRef().killTweensOf(logoEl);
        gsapRef().set(logoEl, { visibility: 'visible' });
        gsapRef().to(logoEl, {
            opacity: 1,
            duration: LOGO_IN_DUR,
            ease: 'power2.out',
            onComplete: done,
        });
    }

    function fadeOutLogo(done) {
        if (!gsapRef() || prefersReducedMotion()) {
            logoEl.style.opacity = '0';
            logoEl.style.visibility = 'hidden';
            if (typeof done === 'function') {
                done();
            }

            return;
        }
        gsapRef().killTweensOf(logoEl);
        gsapRef().to(logoEl, {
            opacity: 0,
            duration: LOGO_OUT_DUR,
            ease: 'power2.inOut',
            onComplete: function () {
                gsapRef().set(logoEl, { visibility: 'hidden' });
                if (typeof done === 'function') {
                    done();
                }
            },
        });
    }

    function waitForFullLoad(callback) {
        function afterLoad() {
            window.setTimeout(callback, EXTRA_WAIT_MS);
        }
        if (document.readyState === 'complete') {
            afterLoad();
        } else {
            window.addEventListener('load', afterLoad, { once: true });
        }
    }

    function prefetchPage(dest, callback) {
        var iframe = document.createElement('iframe');
        iframe.className = 'gp-page-prefetch-frame';
        iframe.setAttribute('aria-hidden', 'true');
        iframe.tabIndex = -1;
        iframe.src = dest;

        var finished = false;
        function finish() {
            if (finished) {
                return;
            }
            finished = true;
            iframe.remove();
            callback();
        }

        iframe.addEventListener('load', finish);
        iframe.addEventListener('error', finish);
        document.body.appendChild(iframe);
        window.setTimeout(finish, 30000);
    }

    function isInternalNavLink(a) {
        if (!a || a.tagName !== 'A') {
            return false;
        }
        var href = a.getAttribute('href');
        if (!href || href.charAt(0) === '#') {
            return false;
        }
        if (a.hasAttribute('download') || a.target === '_blank' || a.hasAttribute('data-bs-toggle')) {
            return false;
        }
        if (a.hasAttribute('data-gp-no-vt') || a.classList.contains('gp-no-vt')) {
            return false;
        }
        if (a.getAttribute('data-gp-flash-dismiss') !== null) {
            return false;
        }
        try {
            var url = new URL(a.href, window.location.href);
            if (url.origin !== window.location.origin) {
                return false;
            }
            if (url.pathname === window.location.pathname && url.search === window.location.search) {
                return false;
            }
        } catch (_) {
            return false;
        }

        return true;
    }

    function runDepartureSequence(dest) {
        navigating = true;

        animateClosePanels(function () {
            fadeInLogo(function () {
                prefetchPage(dest, function () {
                    window.setTimeout(function () {
                        try {
                            sessionStorage.setItem(STORAGE_KEY, 'active');
                        } catch (_) {
                            /* ignore */
                        }
                        window.location.assign(dest);
                    }, EXTRA_WAIT_MS);
                });
            });
        });
    }

    function runArrivalSequence() {
        mountArrivalCurtain();

        waitForFullLoad(function () {
            fadeOutLogo(function () {
                animateOpenPanels();
            });
        });
    }

    function navigateWithTransition(dest) {
        if (navigating || prefersReducedMotion() || !gsapRef()) {
            window.location.assign(dest);

            return;
        }
        runDepartureSequence(dest);
    }

    function bindLinks() {
        document.addEventListener('click', function (e) {
            if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
                return;
            }
            var a = e.target && e.target.closest ? e.target.closest('a[href]') : null;
            if (!isInternalNavLink(a)) {
                return;
            }
            e.preventDefault();
            navigateWithTransition(a.href);
        });
    }

    function boot() {
        var pending = false;
        try {
            pending = sessionStorage.getItem(STORAGE_KEY) === 'active';
        } catch (_) {
            pending = false;
        }

        if (pending) {
            try {
                sessionStorage.removeItem(STORAGE_KEY);
            } catch (_) {
                /* ignore */
            }
            runArrivalSequence();
        } else {
            ensureCurtain();
            setPanelsOpenInstant();
        }

        bindLinks();
    }

    function start() {
        (function waitGsap() {
            if (!gsapRef() && !prefersReducedMotion()) {
                window.setTimeout(waitGsap, 25);

                return;
            }
            boot();
        })();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }

    window.gpNavigateWithTransition = navigateWithTransition;
})();
