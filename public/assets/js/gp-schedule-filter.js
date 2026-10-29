(function () {
    'use strict';

    var root = document.querySelector('[data-gp-class-filter]');
    if (!root) {
        return;
    }

    var panel = root.querySelector('[data-gp-class-filter-panel]');
    var label = root.querySelector('[data-gp-class-filter-label]');
    var clearBtn = root.querySelector('[data-gp-class-filter-clear]');
    var empty = document.querySelector('[data-gp-class-filter-empty]');
    var boxes = panel ? panel.querySelectorAll('input[type="checkbox"]') : [];

    function selected() {
        var ids = [];
        Array.prototype.forEach.call(boxes, function (box) {
            if (box.checked) {
                ids.push(box.value);
            }
        });
        return ids;
    }

    function apply() {
        var ids = selected();
        var showAll = ids.length === 0;
        var allow = {};
        ids.forEach(function (id) {
            allow[id] = true;
        });

        var visibleCount = 0;
        document.querySelectorAll('.gp-schedule-slot').forEach(function (slot) {
            var id = slot.getAttribute('data-actividad-id') || '';
            var show = showAll || !!allow[id];
            slot.hidden = !show;
            if (show) {
                visibleCount += 1;
            }
        });

        document.querySelectorAll('.gp-schedule-row').forEach(function (row) {
            var hasVisible = row.querySelector('.gp-schedule-slot:not([hidden])');
            row.hidden = !showAll && !hasVisible;
        });

        if (empty) {
            empty.hidden = showAll || visibleCount > 0;
        }

        if (!label) {
            return;
        }
        if (showAll) {
            label.textContent = 'Todas las clases';
            return;
        }
        if (ids.length === 1) {
            var only = panel.querySelector('input[type="checkbox"][value="' + ids[0] + '"]');
            label.textContent = (only && only.getAttribute('data-nombre')) || '1 clase';
            return;
        }
        label.textContent = ids.length + ' clases';
    }

    Array.prototype.forEach.call(boxes, function (box) {
        box.addEventListener('change', apply);
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            Array.prototype.forEach.call(boxes, function (box) {
                box.checked = false;
            });
            apply();
        });
    }
})();
