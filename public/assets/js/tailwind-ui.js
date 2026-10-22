/**
 * Spartum UI primitives for the Tailwind build.
 *
 * Replaces the small subset of Bootstrap JavaScript previously used by the
 * application: modals, collapsible navigation, dropdowns, tabs and popovers.
 */
(function () {
    'use strict';

    var modalInstances = new WeakMap();
    var tabInstances = new WeakMap();

    function emit(element, name) {
        element.dispatchEvent(new CustomEvent(name, { bubbles: true }));
    }

    function focusable(element) {
        return element.querySelector(
            'button:not([disabled]), a[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
        );
    }

    function Modal(element, options) {
        this.element = element;
        this.options = Object.assign({ backdrop: true, keyboard: true }, options || {});
        this.previouslyFocused = null;
        modalInstances.set(element, this);
    }

    Modal.getOrCreateInstance = function (element, options) {
        return modalInstances.get(element) || new Modal(element, options);
    };

    Modal.prototype.show = function () {
        if (!this.element || this.element.classList.contains('show')) {
            return;
        }
        this.previouslyFocused = document.activeElement;
        emit(this.element, 'show.gp.modal');
        this.element.hidden = false;
        this.element.setAttribute('aria-hidden', 'false');
        this.element.setAttribute('aria-modal', 'true');
        this.element.setAttribute('role', 'dialog');
        this.element.classList.add('show');
        document.body.classList.add('modal-open');
        var target = focusable(this.element) || this.element;
        window.requestAnimationFrame(function () {
            target.focus({ preventScroll: true });
        });
        emit(this.element, 'shown.gp.modal');
    };

    Modal.prototype.hide = function () {
        if (!this.element || !this.element.classList.contains('show')) {
            return;
        }
        emit(this.element, 'hide.gp.modal');
        this.element.classList.remove('show');
        this.element.setAttribute('aria-hidden', 'true');
        this.element.removeAttribute('aria-modal');
        document.body.classList.remove('modal-open');
        this.element.hidden = true;
        if (this.previouslyFocused && typeof this.previouslyFocused.focus === 'function') {
            this.previouslyFocused.focus({ preventScroll: true });
        }
        emit(this.element, 'hidden.gp.modal');
    };

    function Tab(element) {
        this.element = element;
        tabInstances.set(element, this);
    }

    Tab.getOrCreateInstance = function (element) {
        return tabInstances.get(element) || new Tab(element);
    };

    Tab.prototype.show = function () {
        var targetSelector = this.element.getAttribute('data-gp-target') || this.element.getAttribute('href');
        if (!targetSelector || targetSelector.charAt(0) !== '#') {
            return;
        }
        var target = document.querySelector(targetSelector);
        if (!target) {
            return;
        }
        var tabList = this.element.closest('[role="tablist"], .nav');
        if (tabList) {
            tabList.querySelectorAll('[data-gp-toggle="tab"], [data-gp-toggle="pill"]').forEach(function (button) {
                var paneSelector = button.getAttribute('data-gp-target') || button.getAttribute('href');
                button.classList.remove('active');
                button.setAttribute('aria-selected', 'false');
                if (paneSelector && paneSelector.charAt(0) === '#') {
                    var pane = document.querySelector(paneSelector);
                    if (pane) {
                        pane.classList.remove('active', 'show');
                        pane.hidden = true;
                    }
                }
            });
        }
        this.element.classList.add('active');
        this.element.setAttribute('aria-selected', 'true');
        target.hidden = false;
        target.classList.add('active', 'show');
        emit(this.element, 'shown.gp.tab');
    };

    function Popover(element, options) {
        this.element = element;
        this.options = options || {};
        this.popover = null;
        this.bind();
    }

    Popover.prototype.bind = function () {
        var self = this;
        this.element.addEventListener('mouseenter', function () { self.show(); });
        this.element.addEventListener('focus', function () { self.show(); });
        this.element.addEventListener('mouseleave', function () { self.hide(); });
        this.element.addEventListener('blur', function () { self.hide(); });
    };

    Popover.prototype.show = function () {
        if (this.popover) {
            return;
        }
        var popover = document.createElement('div');
        popover.className = 'popover ' + (this.options.customClass || '');
        popover.setAttribute('role', 'tooltip');
        var title = typeof this.options.title === 'function' ? this.options.title() : this.options.title;
        var content = typeof this.options.content === 'function' ? this.options.content() : this.options.content;
        if (title) {
            var header = document.createElement('div');
            header.className = 'popover-header';
            header.textContent = String(title);
            popover.appendChild(header);
        }
        var body = document.createElement('div');
        body.className = 'popover-body';
        if (this.options.html) {
            body.innerHTML = String(content || '');
        } else {
            body.textContent = String(content || '');
        }
        popover.appendChild(body);
        document.body.appendChild(popover);
        this.popover = popover;
        var anchor = this.element.getBoundingClientRect();
        var box = popover.getBoundingClientRect();
        var left = Math.max(8, Math.min(window.innerWidth - box.width - 8, anchor.left + anchor.width / 2 - box.width / 2));
        var top = anchor.top - box.height - 8;
        if (top < 8) {
            top = anchor.bottom + 8;
        }
        popover.style.left = left + 'px';
        popover.style.top = top + 'px';
    };

    Popover.prototype.hide = function () {
        if (this.popover) {
            this.popover.remove();
            this.popover = null;
        }
    };

    function closeDropdowns(except) {
        document.querySelectorAll('.dropdown-menu.show').forEach(function (menu) {
            if (menu !== except) {
                menu.classList.remove('show');
                var trigger = menu.parentElement && menu.parentElement.querySelector('[data-gp-toggle="dropdown"]');
                if (trigger) {
                    trigger.setAttribute('aria-expanded', 'false');
                }
            }
        });
    }

    function bindDelegatedControls() {
        document.addEventListener('click', function (event) {
            var dismiss = event.target.closest('[data-gp-dismiss="modal"]');
            if (dismiss) {
                var modal = dismiss.closest('.modal');
                if (modal) {
                    event.preventDefault();
                    Modal.getOrCreateInstance(modal).hide();
                }
                return;
            }

            var tab = event.target.closest('[data-gp-toggle="tab"], [data-gp-toggle="pill"]');
            if (tab) {
                event.preventDefault();
                Tab.getOrCreateInstance(tab).show();
                return;
            }

            var collapse = event.target.closest('[data-gp-toggle="collapse"]');
            if (collapse) {
                event.preventDefault();
                var selector = collapse.getAttribute('data-gp-target') || collapse.getAttribute('href');
                var panel = selector ? document.querySelector(selector) : null;
                if (panel) {
                    var open = panel.classList.toggle('show');
                    if (!panel.classList.contains('navbar-collapse')) {
                        panel.hidden = !open;
                    }
                    collapse.setAttribute('aria-expanded', open ? 'true' : 'false');
                }
                return;
            }

            var dropdown = event.target.closest('[data-gp-toggle="dropdown"]');
            if (dropdown) {
                event.preventDefault();
                var menu = dropdown.parentElement && dropdown.parentElement.querySelector('.dropdown-menu');
                if (menu) {
                    var shouldOpen = !menu.classList.contains('show');
                    closeDropdowns(menu);
                    menu.classList.toggle('show', shouldOpen);
                    dropdown.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
                }
                return;
            }

            closeDropdowns(null);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }
            var openModal = document.querySelector('.modal.show');
            if (openModal) {
                var instance = Modal.getOrCreateInstance(openModal);
                if (instance.options.keyboard !== false) {
                    instance.hide();
                }
                return;
            }
            closeDropdowns(null);
        });

        document.addEventListener('mousedown', function (event) {
            var modal = event.target.classList && event.target.classList.contains('modal') ? event.target : null;
            if (!modal) {
                return;
            }
            var instance = Modal.getOrCreateInstance(modal);
            if (instance.options.backdrop !== 'static') {
                instance.hide();
            }
        });
    }

    function initialise() {
        document.querySelectorAll('.modal').forEach(function (element) {
            element.hidden = !element.classList.contains('show');
        });
        document.querySelectorAll('.collapse').forEach(function (element) {
            if (element.classList.contains('navbar-collapse')) {
                element.hidden = false;
                return;
            }
            element.hidden = !element.classList.contains('show');
        });
        document.querySelectorAll('.tab-pane').forEach(function (element) {
            element.hidden = !element.classList.contains('active');
        });
    }

    window.SpartumUI = {
        Modal: Modal,
        Popover: Popover,
        Tab: Tab
    };

    bindDelegatedControls();
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialise);
    } else {
        initialise();
    }
})();
