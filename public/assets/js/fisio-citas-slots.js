/**
 * Carga huecos de 30 min disponibles al elegir fisio + fecha.
 */
(function () {
    'use strict';

    function qs(sel, root) {
        return (root || document).querySelector(sel);
    }

    function initForm(form) {
        var fisio = qs('#fisio_id', form);
        var fecha = qs('#fecha_cita', form);
        var hora = qs('#hora_cita', form);
        var hint = qs('[data-gp-fisio-slots-hint]', form);
        var url = form.getAttribute('data-horas-url') || '';

        if (!fisio || !fecha || !hora || !url) {
            return;
        }

        function setHint(text, isError) {
            if (!hint) {
                return;
            }
            hint.textContent = text;
            hint.classList.toggle('text-danger', !!isError);
            hint.classList.toggle('text-muted', !isError);
        }

        function resetHora(msg) {
            hora.innerHTML = '';
            var opt = document.createElement('option');
            opt.value = '';
            opt.textContent = msg || '— Primero elige fisio y fecha —';
            hora.appendChild(opt);
            hora.disabled = true;
        }

        function cargarHoras() {
            var fid = parseInt(fisio.value, 10);
            var f = (fecha.value || '').trim();
            resetHora('Cargando…');
            setHint('', false);

            if (!Number.isFinite(fid) || fid <= 0 || !/^\d{4}-\d{2}-\d{2}$/.test(f)) {
                resetHora('— Primero elige fisio y fecha —');
                return;
            }

            var reqUrl = url + (url.indexOf('?') >= 0 ? '&' : '?')
                + 'fisio_id=' + encodeURIComponent(String(fid))
                + '&fecha=' + encodeURIComponent(f);

            fetch(reqUrl, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin'
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    hora.innerHTML = '';
                    var lista = Array.isArray(data.horas) ? data.horas : [];

                    if (!data.ok || lista.length === 0) {
                        var msg = 'Sin huecos libres';
                        if (data.dia_cerrado) {
                            msg = 'Centro cerrado ese día';
                        } else if (data.dia_completo) {
                            msg = 'Día completo para este fisio';
                        }
                        resetHora(msg);
                        setHint(
                            data.dia_cerrado
                                ? 'Elige otro día (Lun–Vie 9:00–21:00, Sáb 10:00–14:00).'
                                : 'Prueba otra fecha u otro fisioterapeuta.',
                            true
                        );
                        return;
                    }

                    var placeholder = document.createElement('option');
                    placeholder.value = '';
                    placeholder.textContent = '— Selecciona hora —';
                    hora.appendChild(placeholder);

                    lista.forEach(function (h) {
                        var o = document.createElement('option');
                        o.value = h;
                        o.textContent = h + ' (30 min)';
                        hora.appendChild(o);
                    });

                    hora.disabled = false;
                    setHint(lista.length + ' hueco(s) libre(s) · consultas de 30 min.', false);
                })
                .catch(function () {
                    resetHora('Error al cargar horas');
                    setHint('No se pudieron cargar los huecos. Recarga la página.', true);
                });
        }

        resetHora();
        fisio.addEventListener('change', cargarHoras);
        fecha.addEventListener('change', cargarHoras);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[data-gp-fisio-slots]').forEach(function (form) {
            if (form instanceof HTMLFormElement) {
                initForm(form);
            }
        });
    });
})();
