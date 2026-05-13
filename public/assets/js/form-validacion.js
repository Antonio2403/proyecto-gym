/**
 * Validación de formularios (cliente) alineada con las reglas del servidor.
 * Solo UX: la BD y PHP siguen siendo la fuente de verdad (ver VALIDACIONES_CAPAS.txt).
 * Uso: <form data-gp-validate="login" class="needs-validation" novalidate>
 */
(function () {
    'use strict';

    var LETTERS_DNI = 'TRWAGMYFPDXBNJZSQVHLCKE';

    function trimStr(v) {
        return String(v == null ? '' : v).trim();
    }

    function horarioCentroCfg() {
        return window.GP_HORARIO_CENTRO || { dias: {}, lineas: [] };
    }

    function diaLetraJs(d) {
        var map = { 0: 'D', 1: 'L', 2: 'M', 3: 'X', 4: 'J', 5: 'V', 6: 'S' };
        return map[d.getDay()] || '';
    }

    function minutosHora(hhmm) {
        if (!hhmm || !/^\d{2}:\d{2}$/.test(hhmm)) {
            return NaN;
        }
        var p = hhmm.split(':');
        return parseInt(p[0], 10) * 60 + parseInt(p[1], 10);
    }

    function cabeEnDiaCentro(dia, horaInicio, duracion) {
        var cfg = horarioCentroCfg().dias || {};
        var r = cfg[dia];
        if (!r) {
            return false;
        }
        var ini = minutosHora(horaInicio);
        var fin = ini + duracion;
        var open = minutosHora(r.open);
        var close = minutosHora(r.close);
        return Number.isFinite(ini) && Number.isFinite(fin) && fin <= close && ini >= open;
    }

    function msgHorarioCentro() {
        var lineas = horarioCentroCfg().lineas || [];
        return lineas.length ? ' Horario del centro: ' + lineas.join(', ') + '.' : '';
    }

    function validarProgramacionActividad(form, recurrenteChecked) {
        var horaEl = field(form, 'hora_inicio');
        var hora = horaEl ? trimStr(horaEl.value) : '';
        var durEl = field(form, 'duracion');
        var dur = durEl ? parseInt(durEl.value, 10) : NaN;
        if (!Number.isFinite(dur) || dur < 1) {
            return true;
        }

        if (recurrenteChecked) {
            var marcados = form.querySelectorAll('input[name="dias_semana[]"]:checked');
            if (!marcados || marcados.length < 1) {
                return true;
            }
            for (var i = 0; i < marcados.length; i++) {
                var dia = marcados[i].value;
                if (!cabeEnDiaCentro(dia, hora, dur)) {
                    setValidity(horaEl, 'Fuera del horario de apertura para el día seleccionado.' + msgHorarioCentro());
                    return false;
                }
            }
            return true;
        }

        var fechaEl = field(form, 'fecha_actividad');
        var fecha = fechaEl ? trimStr(fechaEl.value) : '';
        if (!fecha) {
            setValidity(fechaEl, 'Indica la fecha del evento puntual.');
            return false;
        }
        var d = new Date(fecha + 'T12:00:00');
        var dia = diaLetraJs(d);
        if (!cabeEnDiaCentro(dia, hora, dur)) {
            setValidity(fechaEl, 'Esa fecha/hora está fuera del horario del centro.' + msgHorarioCentro());
            return false;
        }
        return true;
    }

    function validarCitaFisioHorario(dtEl) {
        if (!dtEl || trimStr(dtEl.value) === '') {
            return true;
        }
        var parsed = Date.parse(dtEl.value);
        if (!Number.isFinite(parsed)) {
            return true;
        }
        var d = new Date(parsed);
        var dia = diaLetraJs(d);
        var hora = String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
        if (!cabeEnDiaCentro(dia, hora, 30)) {
            dtEl.setCustomValidity('La cita debe estar dentro del horario del centro.' + msgHorarioCentro());
            return false;
        }
        return true;
    }

    function bindRecurrenteFechaPuntual(form) {
        var rec = form.querySelector('input[name="recurrente"]');
        var wrap = form.querySelector('[data-gp-fecha-puntual]');
        var diasWrap = form.querySelector('[data-gp-dias-semana]');
        if (!rec || !wrap) {
            return;
        }
        function sync() {
            var on = rec.checked;
            wrap.hidden = on;
            if (diasWrap) {
                diasWrap.hidden = !on;
            }
            var fechaEl = field(form, 'fecha_actividad');
            if (fechaEl) {
                fechaEl.required = !on;
            }
        }
        rec.addEventListener('change', sync);
        sync();
    }

    function emailOk(val) {
        var t = trimStr(val).toLowerCase();
        return t !== '' && /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(t);
    }

    function telefonoDigitosNacionales(raw) {
        var t = trimStr(raw).replace(/[^0-9]/g, '');
        if (t.indexOf('0034') === 0) t = t.slice(4);
        else if (t.indexOf('34') === 0 && t.length > 9) t = t.slice(2);
        return t;
    }

    function formatTelefono(raw) {
        var t = telefonoDigitosNacionales(raw).slice(0, 9);
        if (t.length <= 3) return t;
        if (t.length <= 5) return t.slice(0, 3) + ' ' + t.slice(3);
        if (t.length <= 7) return t.slice(0, 3) + ' ' + t.slice(3, 5) + ' ' + t.slice(5);
        return t.slice(0, 3) + ' ' + t.slice(3, 5) + ' ' + t.slice(5, 7) + ' ' + t.slice(7, 9);
    }

    function telefonoEsOpcionalOk(raw) {
        var t = telefonoDigitosNacionales(raw);
        if (t === '') return true;
        return /^[6-9]\d{8}$/.test(t);
    }

    function telefonoEsObligatorioOk(raw) {
        return telefonoEsOpcionalOk(raw) && telefonoDigitosNacionales(raw) !== '';
    }

    function dniNieOk(raw) {
        var doc = trimStr(raw).replace(/\s+/g, '').toUpperCase();
        if (!doc) return false;

        var okDni = /^(\d{8})([A-Z])$/.exec(doc);
        if (okDni) {
            var num = parseInt(okDni[1], 10);
            return LETTERS_DNI[num % 23] === okDni[2];
        }

        var okNie = /^([XYZ])(\d{7})([A-Z])$/.exec(doc);
        if (okNie) {
            var map = { X: '0', Y: '1', Z: '2' };
            var numN = parseInt(map[okNie[1]] + okNie[2], 10);
            return LETTERS_DNI[numN % 23] === okNie[3];
        }

        return false;
    }

    /**
     * DNI: 8 dígitos + 1 letra. NIE: X/Y/Z + 7 dígitos + 1 letra. Máximo 9 caracteres, sin espacios.
     */
    function formatDocumentoIdentidadEs(raw) {
        var s = String(raw || '').replace(/\s+/g, '').toUpperCase();
        var out = '';
        var i;
        for (i = 0; i < s.length && out.length < 9; i++) {
            var c = s.charAt(i);
            if (out.length === 0) {
                if (/[0-9XYZ]/.test(c)) {
                    out += c;
                }
                continue;
            }
            if (/^[XYZ]/.test(out)) {
                if (out.length <= 7 && /\d/.test(c)) {
                    out += c;
                } else if (out.length === 8 && /[A-Z]/.test(c)) {
                    out += c;
                }
            } else {
                if (out.length <= 7 && /\d/.test(c)) {
                    out += c;
                } else if (out.length === 8 && /[A-Z]/.test(c)) {
                    out += c;
                }
            }
        }
        return out;
    }

    function claveFuerteOk(raw) {
        var s = trimStr(raw);
        if (s.length < 16) return false;
        if (!/[A-Z]/.test(s)) return false;
        if (!/[a-z]/.test(s)) return false;
        if (!/[0-9]/.test(s)) return false;
        if (!/[^A-Za-z0-9]/.test(s)) return false;
        return true;
    }

    /** @returns {HTMLInputElement|HTMLSelectElement|HTMLTextAreaElement|null} */
    function field(form, name) {
        var el = form.elements.namedItem(name);
        return el instanceof HTMLInputElement ||
            el instanceof HTMLSelectElement ||
            el instanceof HTMLTextAreaElement
            ? el
            : null;
    }

    function clearCustomValidity(form) {
        Array.prototype.forEach.call(form.querySelectorAll('input, select, textarea'), function (el) {
            if ('setCustomValidity' in el) el.setCustomValidity('');
        });
    }

    function setValidity(el, message) {
        if (el && 'setCustomValidity' in el) el.setCustomValidity(message || '');
    }

    function requireNonEmpty(form, nm, msg) {
        var el = field(form, nm);
        if (!el) return true;
        if (trimStr(el.value) === '') {
            el.setCustomValidity(msg);
            return false;
        }
        return true;
    }

    function wireInputs(form) {
        Array.prototype.forEach.call(form.querySelectorAll('input, select, textarea'), function (el) {
            if (el.hasAttribute('readonly')) return;
            var clear = function () {
                if ('setCustomValidity' in el) el.setCustomValidity('');
            };
            el.addEventListener('input', clear);
            el.addEventListener('change', clear);
        });
    }

    var handlers = {};

    handlers.login = function (form) {
        clearCustomValidity(form);
        var ok = true;
        var idf = field(form, 'identificador');
        var cl = field(form, 'clave');
        var rawId = idf ? trimStr(idf.value) : '';
        if (rawId === '') {
            setValidity(idf, 'Indica tu email o DNI/NIE.');
            ok = false;
        } else if (!emailOk(rawId.toLowerCase()) && !dniNieOk(rawId)) {
            setValidity(idf, 'Introduce un email válido o un DNI/NIE correcto.');
            ok = false;
        }
        if (!cl || trimStr(cl.value) === '') {
            setValidity(cl, 'La contraseña es obligatoria.');
            ok = false;
        }
        return ok;
    };

    handlers.register = function (form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'DNI', 'El DNI o NIE es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'nombre', 'El nombre es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'apellido1', 'El primer apellido es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'email', 'El email es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'clave', 'La contraseña es obligatoria.') && ok;
        ok = requireNonEmpty(form, 'clave_confirmar', 'Confirma la contraseña.') && ok;

        var dni = field(form, 'DNI');
        if (dni && trimStr(dni.value) && !dniNieOk(dni.value)) {
            dni.setCustomValidity('DNI o NIE no válido.');
            ok = false;
        }

        var em = field(form, 'email');
        if (em && trimStr(em.value) && !emailOk(em.value)) {
            em.setCustomValidity('Email no válido.');
            ok = false;
        }

        var pw = field(form, 'clave');
        var pw2 = field(form, 'clave_confirmar');
        if (pw && trimStr(pw.value) && !claveFuerteOk(pw.value)) {
            pw.setCustomValidity('La contraseña debe tener al menos 16 caracteres e incluir mayúsculas, minúsculas, números y símbolos.');
            ok = false;
        }
        if (pw && pw2 && trimStr(pw.value) !== trimStr(pw2.value)) {
            pw2.setCustomValidity('Las contraseñas deben coincidir.');
            ok = false;
        }

        var tel = field(form, 'telefono');
        if (tel && trimStr(tel.value) && !telefonoEsOpcionalOk(tel.value)) {
            tel.setCustomValidity('Teléfono no válido: debe tener 9 dígitos y formato 612 34 56 78, o déjalo vacío.');
            ok = false;
        }

        return ok;
    };

    handlers.editClient = function (form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'DNI', 'El DNI o NIE es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'nombre', 'El nombre es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'apellido1', 'El primer apellido es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'email', 'El email es obligatorio.') && ok;

        var dni = field(form, 'DNI');
        if (dni && trimStr(dni.value) && !dniNieOk(dni.value)) {
            dni.setCustomValidity('DNI o NIE no válido.');
            ok = false;
        }

        var em = field(form, 'email');
        if (em && trimStr(em.value) && !emailOk(em.value)) {
            em.setCustomValidity('Email no válido.');
            ok = false;
        }

        var pw = field(form, 'clave');
        var pw2 = field(form, 'clave_confirmar');
        var pv = trimStr(pw ? pw.value : '');
        if (pw && pv.length > 0 && !claveFuerteOk(pv)) {
            pw.setCustomValidity('La nueva contraseña debe tener al menos 16 caracteres e incluir mayúsculas, minúsculas, números y símbolos.');
            ok = false;
        }
        if (pw && pw2 && pv.length > 0 && trimStr(pw2.value) !== pv) {
            pw2.setCustomValidity('Las contraseñas deben coincidir.');
            ok = false;
        }

        var tel = field(form, 'telefono');
        if (tel && trimStr(tel.value) && !telefonoEsOpcionalOk(tel.value)) {
            tel.setCustomValidity('Teléfono no válido: debe tener 9 dígitos y formato 612 34 56 78, o déjalo vacío.');
            ok = false;
        }

        return ok;
    };

    handlers.contact = function (form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'nombre', 'Indica tu nombre.') && ok;
        ok = requireNonEmpty(form, 'email', 'Indica tu email.') && ok;
        ok = requireNonEmpty(form, 'asunto', 'Indica el asunto.') && ok;

        var em = field(form, 'email');
        if (em && trimStr(em.value) && !emailOk(em.value)) {
            em.setCustomValidity('Email no válido.');
            ok = false;
        }

        var tx = field(form, 'mensaje');
        if (!tx || trimStr(tx.value) === '') {
            setValidity(tx, 'Escribe un mensaje.');
            ok = false;
        }

        return ok;
    };

    handlers.activityCreate = function (form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'nombre', 'El nombre es obligatorio.') && ok;

        var dur = field(form, 'duracion');
        var d = dur ? parseInt(dur.value, 10) : NaN;
        if (!dur || !Number.isFinite(d) || d < 1 || d > 600) {
            setValidity(dur, 'Duración entre 1 y 600 minutos.');
            ok = false;
        }

        var marcados = form.querySelectorAll('input[name="dias_semana[]"]:checked');
        if (!marcados || marcados.length < 1) {
            var anyCb = form.querySelector('input[name="dias_semana[]"]');
            setValidity(anyCb, 'Marca al menos un día.');
            ok = false;
        }

        ok = requireNonEmpty(form, 'hora_inicio', 'Indica la hora.') && ok;

        var sala = field(form, 'sala_id');
        var salaV = sala ? parseInt(sala.value, 10) : 0;
        if (!sala || !Number.isFinite(salaV) || salaV <= 0) {
            setValidity(sala, 'Selecciona una sala.');
            ok = false;
        }

        var mon = field(form, 'monitor_id');
        var monV = mon ? parseInt(mon.value, 10) : 0;
        if (!mon || !Number.isFinite(monV) || monV <= 0) {
            setValidity(mon, 'Selecciona un monitor.');
            ok = false;
        }

        var recCb = form.querySelector('input[name="recurrente"]');
        if (!validarProgramacionActividad(form, !recCb || recCb.checked)) {
            ok = false;
        }

        return ok;
    };

    handlers.activityEdit = function (form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'nombre', 'El nombre es obligatorio.') && ok;

        var marcados = form.querySelectorAll('input[name="dias_semana[]"]:checked');
        if (!marcados || marcados.length < 1) {
            var anyCb = form.querySelector('input[name="dias_semana[]"]');
            setValidity(anyCb, 'Marca al menos un día.');
            ok = false;
        }

        var dur = field(form, 'duracion');
        var d = dur ? parseInt(dur.value, 10) : NaN;
        if (!dur || !Number.isFinite(d) || d < 1 || d > 600) {
            setValidity(dur, 'Duración entre 1 y 600 minutos.');
            ok = false;
        }

        ok = requireNonEmpty(form, 'hora_inicio', 'Indica la hora.') && ok;

        var sala = field(form, 'sala_id');
        var salaV = sala ? parseInt(sala.value, 10) : 0;
        if (!sala || !Number.isFinite(salaV) || salaV <= 0) {
            setValidity(sala, 'Selecciona una sala.');
            ok = false;
        }

        var mon = field(form, 'monitor_id');
        var monV = mon ? parseInt(mon.value, 10) : 0;
        if (!mon || !Number.isFinite(monV) || monV <= 0) {
            setValidity(mon, 'Selecciona un monitor.');
            ok = false;
        }

        var recCb = form.querySelector('input[name="recurrente"]');
        if (!validarProgramacionActividad(form, !recCb || recCb.checked)) {
            ok = false;
        }

        return ok;
    };

    handlers.monitorCreate = function (form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'DNI', 'El DNI o NIE es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'nombre', 'El nombre es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'apellido1', 'El primer apellido es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'email', 'El email es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'clave', 'La contraseña es obligatoria.') && ok;
        ok = requireNonEmpty(form, 'clave_confirmar', 'Confirma la contraseña.') && ok;
        ok = requireNonEmpty(form, 'telefono', 'El teléfono es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'especialidad', 'La especialidad es obligatoria.') && ok;
        ok = requireNonEmpty(form, 'disponibilidad', 'La disponibilidad es obligatoria.') && ok;

        var dni = field(form, 'DNI');
        if (dni && trimStr(dni.value) && !dniNieOk(dni.value)) {
            dni.setCustomValidity('DNI o NIE no válido.');
            ok = false;
        }

        var em = field(form, 'email');
        if (em && trimStr(em.value) && !emailOk(em.value)) {
            em.setCustomValidity('Email no válido.');
            ok = false;
        }

        var pw = field(form, 'clave');
        var pw2 = field(form, 'clave_confirmar');
        if (pw && !claveFuerteOk(pw.value)) {
            pw.setCustomValidity('La contraseña debe tener al menos 16 caracteres e incluir mayúsculas, minúsculas, números y símbolos.');
            ok = false;
        }
        if (pw && pw2 && trimStr(pw.value) !== trimStr(pw2.value)) {
            pw2.setCustomValidity('Las contraseñas deben coincidir.');
            ok = false;
        }

        var tel = field(form, 'telefono');
        if (tel && !telefonoEsObligatorioOk(tel.value)) {
            tel.setCustomValidity('Teléfono obligatorio: 9 dígitos con formato 612 34 56 78.');
            ok = false;
        }

        return ok;
    };

    handlers.monitorEdit = function (form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'DNI', 'El DNI o NIE es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'nombre', 'El nombre es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'apellido1', 'El primer apellido es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'email', 'El email es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'telefono', 'El teléfono es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'especialidad', 'La especialidad es obligatoria.') && ok;
        ok = requireNonEmpty(form, 'disponibilidad', 'La disponibilidad es obligatoria.') && ok;

        var dni = field(form, 'DNI');
        if (dni && trimStr(dni.value) && !dniNieOk(dni.value)) {
            dni.setCustomValidity('DNI o NIE no válido.');
            ok = false;
        }

        var em = field(form, 'email');
        if (em && trimStr(em.value) && !emailOk(em.value)) {
            em.setCustomValidity('Email no válido.');
            ok = false;
        }

        var pw = field(form, 'clave');
        var pw2 = field(form, 'clave_confirmar');
        var pv = pw ? trimStr(pw.value) : '';
        if (pw && pv.length > 0 && !claveFuerteOk(pv)) {
            pw.setCustomValidity('La contraseña debe tener al menos 16 caracteres e incluir mayúsculas, minúsculas, números y símbolos.');
            ok = false;
        }
        if (pw && pw2 && pv.length > 0 && trimStr(pw2.value) !== pv) {
            pw2.setCustomValidity('Las contraseñas deben coincidir.');
            ok = false;
        }

        var tel = field(form, 'telefono');
        if (tel && !telefonoEsObligatorioOk(tel.value)) {
            tel.setCustomValidity('Teléfono obligatorio: 9 dígitos con formato 612 34 56 78.');
            ok = false;
        }

        return ok;
    };

    handlers.changePassword = function (form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'clave_actual', 'Indica tu contraseña actual.') && ok;
        ok = requireNonEmpty(form, 'clave_nueva', 'Indica la nueva contraseña.') && ok;
        ok = requireNonEmpty(form, 'clave_nueva2', 'Confirma la nueva contraseña.') && ok;
        var n1 = field(form, 'clave_nueva');
        var n2 = field(form, 'clave_nueva2');
        if (n1 && trimStr(n1.value) && !claveFuerteOk(n1.value)) {
            n1.setCustomValidity('La nueva contraseña debe cumplir los requisitos de complejidad.');
            ok = false;
        }
        if (n1 && n2 && trimStr(n1.value) !== trimStr(n2.value)) {
            n2.setCustomValidity('Las contraseñas nuevas deben coincidir.');
            ok = false;
        }
        return ok;
    };

    handlers.recoverEmail = function (form) {
        clearCustomValidity(form);
        var ok = true;
        var em = field(form, 'email');
        if (!em || !emailOk(em.value)) {
            setValidity(em, 'Introduce un email válido.');
            ok = false;
        }
        return ok;
    };

    handlers.recoveryDniPhone = function (form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'dni', 'El DNI o NIE es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'telefono', 'El teléfono es obligatorio.') && ok;
        var dni = field(form, 'dni');
        if (dni && trimStr(dni.value) && !dniNieOk(dni.value)) {
            dni.setCustomValidity('DNI o NIE no válido.');
            ok = false;
        }
        var tel = field(form, 'telefono');
        if (tel && !telefonoEsObligatorioOk(tel.value)) {
            tel.setCustomValidity('Teléfono: 9 dígitos con formato 612 34 56 78.');
            ok = false;
        }
        return ok;
    };

    handlers.recoveryDniCode = function (form) {
        clearCustomValidity(form);
        var ok = true;
        var sid = field(form, 'solicitud_id');
        if (!sid || trimStr(sid.value) === '' || parseInt(sid.value, 10) <= 0) {
            setValidity(sid, 'Falta el ticket enlazado. Vuelve a «Ya tengo el código» desde la página del ticket.');
            ok = false;
        }
        var inp = field(form, 'codigo_verificacion');
        if (!inp || trimStr(inp.value) === '') {
            setValidity(inp, 'Introduce el código de 6 dígitos que te ha dado recepción.');
            ok = false;
        }
        if (inp && trimStr(inp.value)) {
            var t = trimStr(inp.value).replace(/[^0-9]/g, '');
            if (t.length !== 6) {
                inp.setCustomValidity('El código tiene 6 dígitos (formato 123-456).');
                ok = false;
            }
        }
        return ok;
    };

    handlers.recoveryDniPhoneCode = function (form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'dni', 'El DNI o NIE es obligatorio.') && ok;
        ok = requireNonEmpty(form, 'telefono', 'El teléfono es obligatorio.') && ok;
        var dni = field(form, 'dni');
        if (dni && trimStr(dni.value) && !dniNieOk(dni.value)) {
            dni.setCustomValidity('DNI o NIE no válido.');
            ok = false;
        }
        var tel = field(form, 'telefono');
        if (tel && !telefonoEsObligatorioOk(tel.value)) {
            tel.setCustomValidity('Teléfono: 9 dígitos con formato 612 34 56 78.');
            ok = false;
        }
        var inp = field(form, 'codigo_verificacion');
        if (!inp || trimStr(inp.value) === '') {
            setValidity(inp, 'Introduce el código de 6 dígitos que te ha dado recepción.');
            ok = false;
        }
        if (inp && trimStr(inp.value)) {
            var t = trimStr(inp.value).replace(/[^0-9]/g, '');
            if (t.length !== 6) {
                inp.setCustomValidity('El código tiene 6 dígitos (formato 123-456).');
                ok = false;
            }
        }
        return ok;
    };

    handlers.resetPassword = function (form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'clave_nueva', 'Indica la nueva contraseña.') && ok;
        ok = requireNonEmpty(form, 'clave_nueva2', 'Confirma la contraseña.') && ok;
        var n1 = field(form, 'clave_nueva');
        var n2 = field(form, 'clave_nueva2');
        if (n1 && trimStr(n1.value) && !claveFuerteOk(n1.value)) {
            n1.setCustomValidity('La contraseña debe cumplir los requisitos de complejidad.');
            ok = false;
        }
        if (n1 && n2 && trimStr(n1.value) !== trimStr(n2.value)) {
            n2.setCustomValidity('Las contraseñas deben coincidir.');
            ok = false;
        }
        return ok;
    };

    function validateSubscription(form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'nombre', 'El nombre es obligatorio.') && ok;

        var pr = field(form, 'precio');
        var prV = pr ? trimStr(pr.value) : '';
        if (!pr || prV === '' || !isFinite(parseFloat(prV)) || parseFloat(prV) < 0) {
            setValidity(pr, 'Precio no válido (≥ 0).');
            ok = false;
        }

        var dur = field(form, 'duracion');
        var ds = dur ? trimStr(dur.value) : '';
        if (!dur || ds === '') {
            setValidity(dur, 'La duración es obligatoria.');
            ok = false;
        } else if (!/^\d+$/.test(ds)) {
            setValidity(dur, 'Duración debe ser un número entero de meses.');
            ok = false;
        } else {
            var dInt = parseInt(ds, 10);
            if (dInt < 1 || dInt > 120) {
                setValidity(dur, 'Duración entre 1 y 120 meses.');
                ok = false;
            }
        }

        var clases = field(form, 'numero_clases');
        var cv = clases ? trimStr(clases.value) : '';
        if (!clases || cv === '' || !/^\d+$/.test(cv)) {
            setValidity(clases, 'Indica las clases por semana (0 si no hay límite).');
            ok = false;
        } else {
            var cInt = parseInt(cv, 10);
            if (cInt < 0 || cInt > 99) {
                setValidity(clases, 'Clases por semana entre 0 y 99.');
                ok = false;
            }
        }

        var fisio = field(form, 'fisio');
        if (!fisio || ['S', 'N'].indexOf(fisio.value) === -1) {
            setValidity(fisio, 'Selecciona si incluye fisioterapia.');
            ok = false;
        }

        return ok;
    }

    handlers.subscriptionCreate = validateSubscription;
    handlers.subscriptionEdit = validateSubscription;

    handlers.salaCreate = function (form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'nombre', 'El nombre es obligatorio.') && ok;

        var cap = field(form, 'capacidad');
        var c = cap ? parseInt(cap.value, 10) : NaN;
        if (!cap || !Number.isFinite(c) || c < 1 || c > 10000) {
            setValidity(cap, 'Capacidad entre 1 y 10000.');
            ok = false;
        }

        var disp = field(form, 'disponibilidad');
        if (!disp || trimStr(disp.value) === '') {
            setValidity(disp, 'Selecciona disponibilidad.');
            ok = false;
        }

        return ok;
    };

    handlers.salaEdit = function (form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'nombre', 'El nombre es obligatorio.') && ok;

        var cap = field(form, 'capacidad');
        var c = cap ? parseInt(cap.value, 10) : NaN;
        if (!cap || !Number.isFinite(c) || c < 1 || c > 10000) {
            setValidity(cap, 'Capacidad entre 1 y 10000.');
            ok = false;
        }

        ok = requireNonEmpty(form, 'disponibilidad', 'La disponibilidad es obligatoria.') && ok;

        return ok;
    };

    handlers.materialCreate = function (form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'nombre', 'El nombre es obligatorio.') && ok;

        var st = field(form, 'estado');
        if (!st || (st.value !== 'B' && st.value !== 'M')) {
            setValidity(st, 'Selecciona un estado válido.');
            ok = false;
        }

        return ok;
    };

    handlers.materialEdit = handlers.materialCreate;

    handlers.monitorSolicitud = function (form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'tipo', 'El tipo de solicitud es obligatorio.') && ok;

        var t = field(form, 'tipo');
        if (t && trimStr(t.value).length > 120) {
            t.setCustomValidity('El tipo no puede superar 120 caracteres.');
            ok = false;
        }

        var d = field(form, 'descripcion');
        if (d && trimStr(d.value).length > 4000) {
            d.setCustomValidity('La descripción no puede superar 4000 caracteres.');
            ok = false;
        }

        return ok;
    };

    handlers.comments = function (form) {
        clearCustomValidity(form);
        var tx = field(form, 'texto');
        var text = trimStr(tx ? tx.value : '');
        if (!tx || text.length < 3) {
            setValidity(tx, 'El comentario debe tener al menos 3 caracteres.');
            return false;
        }
        if (text.length > 2000) {
            tx.setCustomValidity('Máximo 2000 caracteres.');
            return false;
        }
        return true;
    };

    handlers.fisioRequest = function (form) {
        clearCustomValidity(form);
        var ok = true;

        var fis = field(form, 'fisio_id');
        var fid = fis ? parseInt(fis.value, 10) : 0;
        if (!fis || !Number.isFinite(fid) || fid <= 0) {
            setValidity(fis, 'Selecciona un fisioterapeuta.');
            ok = false;
        }

        var fechaEl = field(form, 'fecha_cita');
        var fecha = fechaEl ? trimStr(fechaEl.value) : '';
        if (!fechaEl || !/^\d{4}-\d{2}-\d{2}$/.test(fecha)) {
            setValidity(fechaEl, 'Indica la fecha.');
            ok = false;
        }

        var horaEl = field(form, 'hora_cita');
        var hora = horaEl ? trimStr(horaEl.value) : '';
        if (!horaEl || hora === '' || horaEl.disabled) {
            setValidity(horaEl, 'Elige una hora disponible.');
            ok = false;
        } else if (!/^\d{2}:\d{2}$/.test(hora)) {
            setValidity(horaEl, 'Hora no válida.');
            ok = false;
        } else if (fecha !== '') {
            var parsed = Date.parse(fecha + 'T' + hora + ':00');
            if (!Number.isFinite(parsed) || parsed < Date.now() - 5000) {
                setValidity(horaEl, 'Elige un hueco futuro.');
                ok = false;
            } else {
                var fakeDt = { value: fecha + 'T' + hora };
                if (!validarCitaFisioHorario(fakeDt)) {
                    ok = false;
                }
            }
        }

        ok = requireNonEmpty(form, 'motivo', 'Describe el motivo.') && ok;

        var mv = field(form, 'motivo');
        if (mv && trimStr(mv.value).length > 2000) {
            mv.setCustomValidity('Máximo 2000 caracteres.');
            ok = false;
        }

        return ok;
    };

    handlers.adminFisio = function (form) {
        clearCustomValidity(form);
        var ok = true;

        ok = requireNonEmpty(form, 'nombre', 'El nombre es obligatorio.') && ok;

        var nm = field(form, 'nombre');
        if (nm && trimStr(nm.value).length > 100) {
            nm.setCustomValidity('Máximo 100 caracteres.');
            ok = false;
        }

        var esp = field(form, 'especialidad');
        if (esp && trimStr(esp.value).length > 100) {
            esp.setCustomValidity('Máximo 100 caracteres.');
            ok = false;
        }

        var hidden = field(form, 'id');
        if (hidden && trimStr(hidden.value) !== '') {
            var idNum = parseInt(hidden.value, 10);
            if (!Number.isFinite(idNum) || idNum <= 0) {
                hidden.setCustomValidity('Identificador no válido.');
                ok = false;
            }
        }

        return ok;
    };

    function attachForm(form, typeKey) {
        var fn = handlers[typeKey];
        if (!fn) return;

        wireInputs(form);

        form.addEventListener('submit', function (evt) {
            fn(form);
            form.classList.add('was-validated');
            if (!form.checkValidity()) {
                evt.preventDefault();
                evt.stopPropagation();
                var bad = form.querySelector(':invalid');
                if (bad && typeof bad.reportValidity === 'function') {
                    bad.reportValidity();
                }
            }
        });
    }

    function currentCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? trimStr(meta.getAttribute('content') || '') : '';
    }

    function ensureCsrfInput(form) {
        if (!form || String(form.method || '').toLowerCase() !== 'post') return;
        var token = currentCsrfToken();
        if (!token) return;
        var inp = form.querySelector('input[name="csrf_token"]');
        if (!inp) {
            inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'csrf_token';
            form.appendChild(inp);
        }
        inp.value = token;
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('submit', function (evt) {
            if (evt.target instanceof HTMLFormElement) {
                ensureCsrfInput(evt.target);
            }
        }, true);

        document.querySelectorAll('form[data-gp-validate]').forEach(function (form) {
            if (!(form instanceof HTMLFormElement)) return;
            var key = trimStr(form.getAttribute('data-gp-validate'));
            attachForm(form, key);
            if (key === 'activityCreate' || key === 'activityEdit') {
                bindRecurrenteFechaPuntual(form);
            }
        });

        function collectRevealIds(btn) {
            var ids = [];
            var singleId = btn.getAttribute('data-gp-pass-reveal');
            var groupRaw = btn.getAttribute('data-gp-pass-reveal-group');
            if (singleId && trimStr(singleId)) ids.push(trimStr(singleId));
            if (groupRaw) {
                groupRaw.split(',').forEach(function (s) {
                    var id = trimStr(s);
                    if (id && ids.indexOf(id) === -1) ids.push(id);
                });
            }
            return ids;
        }

        function wirePressHoldReveal(btn) {
            var ids = collectRevealIds(btn);
            if (ids.length === 0) return;
            function els() {
                return ids.map(function (id) { return document.getElementById(id); }).filter(function (el) {
                    return el && 'type' in el;
                });
            }
            function showPlain(show) {
                els().forEach(function (el) {
                    el.type = show ? 'text' : 'password';
                });
            }
            function pe(e) {
                if (e) e.preventDefault();
            }
            function start(e) {
                pe(e);
                showPlain(true);
            }
            function end() {
                showPlain(false);
            }
            btn.addEventListener('mousedown', start);
            btn.addEventListener('touchstart', start, { passive: false });
            btn.addEventListener('mouseup', end);
            btn.addEventListener('mouseleave', end);
            btn.addEventListener('touchend', end);
            btn.addEventListener('touchcancel', end);
            btn.addEventListener('keydown', function (e) {
                if (e.key === ' ' || e.key === 'Enter') {
                    pe(e);
                    showPlain(true);
                }
            });
            btn.addEventListener('keyup', function (e) {
                if (e.key === ' ' || e.key === 'Enter') end();
            });
            btn.addEventListener('blur', end);
        }

        document.querySelectorAll('[data-gp-pass-reveal], [data-gp-pass-reveal-group]').forEach(function (btn) {
            wirePressHoldReveal(btn);
        });

        function wireTelefonoFormatter(input) {
            if (!input) return;
            function applyFormat() {
                input.value = formatTelefono(input.value);
            }
            input.addEventListener('input', applyFormat);
            input.addEventListener('blur', applyFormat);
            input.addEventListener('paste', function (ev) {
                var txt = '';
                try {
                    txt = (ev.clipboardData || window.clipboardData).getData('text') || '';
                } catch (e) {
                    return;
                }
                ev.preventDefault();
                input.value = formatTelefono(txt);
                try {
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                } catch (e2) {
                }
            });
            applyFormat();
        }

        document.querySelectorAll('[data-gp-phone-input]').forEach(function (input) {
            wireTelefonoFormatter(input);
        });

        function wireDocumentoIdentidadEs(input) {
            if (!input) {
                return;
            }
            function applyDoc() {
                input.value = formatDocumentoIdentidadEs(input.value);
            }
            input.addEventListener('input', applyDoc);
            input.addEventListener('blur', applyDoc);
            input.addEventListener('paste', function (ev) {
                var txt = '';
                try {
                    txt = (ev.clipboardData || window.clipboardData).getData('text') || '';
                } catch (e) {
                    return;
                }
                ev.preventDefault();
                input.value = formatDocumentoIdentidadEs(txt);
                try {
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                } catch (e2) {
                }
            });
            applyDoc();
        }

        document.querySelectorAll('[data-gp-doc-identidad-es]').forEach(function (input) {
            wireDocumentoIdentidadEs(input);
        });

        function evalClaveFuerteParts(s) {
            return {
                len: s.length >= 16,
                upper: /[A-Z]/.test(s),
                lower: /[a-z]/.test(s),
                num: /[0-9]/.test(s),
                sym: /[^A-Za-z0-9]/.test(s)
            };
        }

        function wirePasswordStrength(input, listRoot) {
            if (!input || !listRoot) return;
            var touched = false;
            function setIdle(key) {
                var li = listRoot.querySelector('[data-rule="' + key + '"]');
                if (!li) return;
                li.classList.remove('gp-pass-rule--ok', 'gp-pass-rule--fail');
                li.classList.add('gp-pass-rule--idle');
            }
            function paint() {
                var s = trimStr(input.value);
                if (!touched && s.length === 0) {
                    ['len', 'upper', 'lower', 'num', 'sym'].forEach(setIdle);
                    return;
                }
                touched = true;
                var p = evalClaveFuerteParts(s);
                ['len', 'upper', 'lower', 'num', 'sym'].forEach(function (key) {
                    var li = listRoot.querySelector('[data-rule="' + key + '"]');
                    if (!li) return;
                    li.classList.remove('gp-pass-rule--idle');
                    var ok = p[key];
                    li.classList.toggle('gp-pass-rule--ok', ok);
                    li.classList.toggle('gp-pass-rule--fail', !ok);
                });
            }
            input.addEventListener('input', paint);
            input.addEventListener('change', paint);
            ['len', 'upper', 'lower', 'num', 'sym'].forEach(setIdle);
        }

        document.querySelectorAll('[data-gp-pass-rules-for]').forEach(function (listRoot) {
            var id = trimStr(listRoot.getAttribute('data-gp-pass-rules-for'));
            if (!id) return;
            var input = document.getElementById(id);
            wirePasswordStrength(input, listRoot);
        });
    });
})();
