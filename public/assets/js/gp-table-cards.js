/**
 * En el móvil, las tablas de datos se leen como tarjetas.
 * El horario semanal (gp-schedule-grid) no se toca.
 */
(function () {
    'use strict';

    var TITLE = {
        nombre: true,
        actividad: true,
        cliente: true,
        asunto: true,
        titulo: true,
        sala: true,
        profesional: true,
        fecha: true
    };

    function norm(value) {
        return String(value || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }

    function headersOf(table) {
        return Array.prototype.map.call(table.querySelectorAll('thead th'), function (th) {
            return (th.textContent || '').replace(/\s+/g, ' ').trim();
        });
    }

    function isSchedule(table) {
        return table.classList.contains('gp-schedule-grid') || !!table.closest('.gp-schedule-layout');
    }

    function isActions(td, label) {
        var key = norm(label);
        if (key === 'acciones' || key === 'accion') {
            return true;
        }
        if (td.classList.contains('gp-actions-cell')) {
            return true;
        }

        return !!td.querySelector('button, form, .gp-btn-action, .gp-actions-stack');
    }

    function titleIndex(heads) {
        var i;
        var key;
        for (i = 0; i < heads.length; i += 1) {
            key = norm(heads[i]).split(/[^a-z0-9]+/)[0];
            if (TITLE[key]) {
                return i;
            }
        }
        for (i = 0; i < heads.length; i += 1) {
            key = norm(heads[i]);
            if (!key || key === 'id' || key.indexOf('id ') === 0 || key === 'color' || key === 'acciones' || key === 'accion') {
                continue;
            }
            return i;
        }

        return -1;
    }

    function labelTable(table) {
        if (!table || isSchedule(table)) {
            table && table.classList.remove('gp-as-cards');
            return;
        }
        var heads = headersOf(table);
        if (heads.length < 2) {
            return;
        }
        var titleAt = titleIndex(heads);
        table.classList.add('gp-as-cards');
        Array.prototype.forEach.call(table.querySelectorAll('tbody > tr'), function (tr) {
            var cells = Array.prototype.filter.call(tr.children, function (el) {
                return el.tagName === 'TD';
            });
            if (cells.length === 1 && cells[0].colSpan > 1) {
                tr.classList.add('gp-card-empty');
                cells[0].classList.remove('gp-card-title', 'gp-card-actions');
                cells[0].removeAttribute('data-label');
                return;
            }
            tr.classList.remove('gp-card-empty');
            cells.forEach(function (td, index) {
                var label = heads[index] || '';
                var actions = isActions(td, label);
                td.classList.toggle('gp-card-actions', actions);
                td.classList.toggle('gp-card-title', index === titleAt && !actions);
                if (!label && actions) {
                    label = 'Acción';
                }
                if (label && index !== titleAt) {
                    td.setAttribute('data-label', label);
                } else {
                    td.removeAttribute('data-label');
                }
            });
        });
    }

    function apply(root) {
        var scope = root && root.querySelectorAll ? root : document;
        if (scope.matches && scope.matches('table')) {
            labelTable(scope);
        }
        Array.prototype.forEach.call(scope.querySelectorAll('table'), labelTable);
    }

    var queued = false;
    function schedule() {
        if (queued) {
            return;
        }
        queued = true;
        window.requestAnimationFrame(function () {
            queued = false;
            apply(document);
        });
    }

    function start() {
        apply(document);
        if (!window.MutationObserver || !document.body) {
            return;
        }
        var observer = new MutationObserver(schedule);
        observer.observe(document.body, { childList: true, subtree: true });
    }

    window.gpApplyTableCards = apply;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
