/**
 * Guía de presentación. Los pasos cambian según el rol de la sesión.
 */
(function () {
    'use strict';

    var GUIAS = {
        visita: [
            ['Bienvenida', 'Esta guía recorre Spartum, el gimnasio de Lepe. Puedes cerrarla cuando quieras y volver a abrirla desde Guía.'],
            ['Portada', 'Arriba está el eslogan. Debajo, que es un gimnasio en Lepe y el horario del centro: lunes a viernes de 9:00 a 21:00 y sábados de 10:00 a 14:00.'],
            ['Horario', 'En el móvil eliges el día. Cada clase se llama sala/actividad y el color de al lado es el de la sala. En el ordenador se ve la cuadrícula de la semana.'],
            ['Reserva y opiniones', 'Apuntarse guarda la plaza. Opiniones enseña lo que se ha escrito de esa actividad, también de otras semanas. Si ya fuiste y la clase terminó, sale Comentar.'],
            ['Planes', 'Cada plan dice el precio, la duración y las clases por semana. Sin plan activo no se puede reservar.'],
            ['Cuenta', 'Entrar, contacto y quiénes somos están en el menú. Si una cuenta está baneada, al intentar entrar se ve el aviso y el motivo.']
        ],
        cliente: [
            ['Tu cuenta', 'Desde el menú llegas a tu panel, las clases, tus reservas, los planes y la fisioterapia.'],
            ['Horario', 'Elige el día. La clase se muestra como sala/actividad, con el color de la sala. Apuntarse pide la plaza y Opiniones abre los comentarios.'],
            ['Después de la clase', 'Si estuviste y la sesión ya terminó, el botón pasa a Comentar. Ese comentario sigue viéndose las semanas siguientes.'],
            ['Planes', 'El plan marca cuántas clases puedes reservar cada semana. Sin un plan en vigor, las reservas quedan en pausa.'],
            ['Fisioterapia', 'Si tu plan la incluye, pides cita y consultas las que ya tienes.']
        ],
        monitor: [
            ['Panel de monitor', 'Desde aquí ves tus clases y las salas del centro.'],
            ['Salas', 'Cada sala tiene un color. Ese color es el que sale en el horario, junto al nombre sala/actividad.'],
            ['Materiales', 'En cada sala, Materiales abre el inventario. Editar un material se hace en un formulario.'],
            ['Clases', 'En el horario del móvil solo aparecen las del día elegido, con Apuntarse y Opiniones.']
        ],
        fisio: [
            ['Panel de fisio', 'Aquí gestionas las citas de fisioterapia.'],
            ['Citas', 'Ves las solicitadas y las confirmadas. Puedes confirmar o atender cada una.'],
            ['Horario del centro', 'Las citas tienen que caer en el horario de Spartum: lunes a viernes de 9:00 a 21:00 y sábados de 10:00 a 14:00.']
        ],
        admin: [
            ['Panel', 'El inicio del panel resume la semana: ocupación, reservas y planes.'],
            ['Usuarios activos', 'Son las cuentas sin baneo. Desde ahí se ve el plan y se puede dar de baja o banear.'],
            ['Usuarios no activos', 'La baja normal se reactiva con Dar alta. El baneo se quita con Desbanear y enseña el motivo.'],
            ['Banear', 'Al banear se abre un formulario para escribir el motivo. Si esa persona intenta entrar, ve «Estás baneado» y ese texto.'],
            ['Actividades y salas', 'Las clases se programan en Actividades. El color no va en la clase: va en la sala, y el horario lo usa.'],
            ['Planes', 'En Suscripciones se cambia el precio, la duración y las clases por semana.']
        ]
    };

    function rolActual() {
        var rol = document.body ? document.body.getAttribute('data-gp-rol') : '';
        return GUIAS[rol] ? rol : 'visita';
    }

    function crear() {
        var root = document.createElement('div');
        root.className = 'gp-tour';
        root.hidden = true;
        root.setAttribute('role', 'dialog');
        root.setAttribute('aria-modal', 'true');
        root.setAttribute('aria-labelledby', 'gpTourTitle');
        root.innerHTML = ''
            + '<div class="gp-tour__card">'
            + '<p class="gp-tour__kicker" data-gp-tour-kicker></p>'
            + '<h2 class="gp-tour__title" id="gpTourTitle" data-gp-tour-title></h2>'
            + '<p class="gp-tour__text" data-gp-tour-text></p>'
            + '<div class="gp-tour__progress" data-gp-tour-progress></div>'
            + '<div class="gp-tour__actions">'
            + '<button type="button" class="btn btn-outline-secondary btn-sm" data-gp-tour-prev>Anterior</button>'
            + '<button type="button" class="btn btn-primary btn-sm gp-btn-plain" data-gp-tour-next>Siguiente</button>'
            + '</div>'
            + '<button type="button" class="gp-tour__close" data-gp-tour-close>Cerrar guía</button>'
            + '</div>';
        document.body.appendChild(root);
        return root;
    }

    var root = null;
    var pasos = [];
    var indice = 0;

    function pintar() {
        var paso = pasos[indice];
        root.querySelector('[data-gp-tour-kicker]').textContent = 'Paso ' + (indice + 1) + ' de ' + pasos.length;
        root.querySelector('[data-gp-tour-title]').textContent = paso[0];
        root.querySelector('[data-gp-tour-text]').textContent = paso[1];
        var prev = root.querySelector('[data-gp-tour-prev]');
        var next = root.querySelector('[data-gp-tour-next]');
        prev.hidden = indice === 0;
        next.textContent = indice === pasos.length - 1 ? 'Listo' : 'Siguiente';
        var bar = root.querySelector('[data-gp-tour-progress]');
        bar.innerHTML = '';
        pasos.forEach(function (_, i) {
            var dot = document.createElement('span');
            dot.className = 'gp-tour__dot' + (i === indice ? ' is-on' : '');
            bar.appendChild(dot);
        });
    }

    function abrir() {
        if (!root) {
            root = crear();
            root.addEventListener('click', function (ev) {
                if (ev.target === root || ev.target.closest('[data-gp-tour-close]')) {
                    cerrar();
                    return;
                }
                if (ev.target.closest('[data-gp-tour-prev]')) {
                    indice = Math.max(0, indice - 1);
                    pintar();
                    return;
                }
                if (ev.target.closest('[data-gp-tour-next]')) {
                    if (indice >= pasos.length - 1) {
                        cerrar();
                        return;
                    }
                    indice += 1;
                    pintar();
                }
            });
            document.addEventListener('keydown', function (ev) {
                if (!root || root.hidden) {
                    return;
                }
                if (ev.key === 'Escape') {
                    cerrar();
                } else if (ev.key === 'ArrowRight') {
                    root.querySelector('[data-gp-tour-next]').click();
                } else if (ev.key === 'ArrowLeft' && indice > 0) {
                    root.querySelector('[data-gp-tour-prev]').click();
                }
            });
        }
        pasos = GUIAS[rolActual()];
        indice = 0;
        pintar();
        root.hidden = false;
        document.body.classList.add('gp-tour-open');
    }

    function cerrar() {
        if (!root) {
            return;
        }
        root.hidden = true;
        document.body.classList.remove('gp-tour-open');
    }

    document.addEventListener('click', function (ev) {
        if (ev.target.closest('[data-gp-tour-open]')) {
            ev.preventDefault();
            abrir();
        }
    });
})();
