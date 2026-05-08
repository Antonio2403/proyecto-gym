/**
 * Validación de formularios (cliente) alineada con las reglas del servidor.
 * Uso: <form data-gp-validate="login" class="needs-validation" novalidate>
 */
(function () {
    'use strict';

    var LETTERS_DNI = 'TRWAGMYFPDXBNJZSQVHLCKE';

    function trimStr(v) {
        return String(v == null ? '' : v).trim();
    }

    function emailOk(val) {
        var t = trimStr(val).toLowerCase();
        return t !== '' && /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(t);
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
        var em = field(form, 'email');
        var cl = field(form, 'clave');
        if (!emailOk(em ? em.value : '')) {
            setValidity(em, 'Introduce un email válido.');
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
        if (pw && trimStr(pw.value).length > 0 && trimStr(pw.value).length < 8) {
            pw.setCustomValidity('La contraseña debe tener al menos 8 caracteres.');
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
        var pv = trimStr(pw ? pw.value : '');
        if (pw && pv.length > 0 && pv.length < 8) {
            pw.setCustomValidity('La nueva contraseña debe tener al menos 8 caracteres.');
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

        var dia = field(form, 'dia_semana');
        var dias = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
        if (!dia || dias.indexOf(trimStr(dia.value)) === -1) {
            setValidity(dia, 'Elige un día de la semana.');
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

        return ok;
    };

    handlers.activityEdit = function (form) {
        clearCustomValidity(form);
        var ok = true;
        ok = requireNonEmpty(form, 'nombre', 'El nombre es obligatorio.') && ok;

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
        if (pw && trimStr(pw.value).length < 8) {
            pw.setCustomValidity('La contraseña debe tener al menos 8 caracteres.');
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
        if (pw && trimStr(pw.value).length > 0 && trimStr(pw.value).length < 8) {
            pw.setCustomValidity('La contraseña debe tener al menos 8 caracteres.');
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

        var dt = field(form, 'fecha_hora');
        if (!dt || trimStr(dt.value) === '') {
            setValidity(dt, 'Indica fecha y hora.');
            ok = false;
        } else {
            var parsed = Date.parse(dt.value);
            if (!Number.isFinite(parsed) || parsed < Date.now() - 5000) {
                setValidity(dt, 'Elige una fecha y hora futuras.');
                ok = false;
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

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[data-gp-validate]').forEach(function (form) {
            if (!(form instanceof HTMLFormElement)) return;
            var key = trimStr(form.getAttribute('data-gp-validate'));
            attachForm(form, key);
        });
    });
})();
