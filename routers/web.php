<?php

require_once __DIR__ . '/../core/helpers/form_validacion.php';

function controller($router, $method, $route, $action)
{
    $router->$method($route, function (...$params) use ($action) {
        if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? '')) === 'POST' && !csrf_validate_request()) {
            http_response_code(419);
            if (str_contains((string) ($_SERVER['CONTENT_TYPE'] ?? ''), 'application/json')) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => false, 'error' => 'Sesión caducada. Recarga la página e inténtalo de nuevo.']);
            } else {
                header('Location: ' . url('/404'));
            }
            exit;
        }

        list($controller, $method) = explode('@', $action);

        require_once "app/controladores/" . $controller . ".php";

        $controller = new $controller();

        call_user_func_array([$controller, $method], $params);
    });
}

//Inicio
controller($router, 'get', '/', 'InicioControlador@index');
controller($router, 'get', '/inicio', 'InicioControlador@index');
controller($router, 'get', '/quienes-somos', 'InicioControlador@quienesSomos');
controller($router, 'get', '/inicioAdmin', 'InicioControlador@inicioAdmin');
controller($router, 'get', '/inicioUsuario', 'InicioControlador@inicioUsuario');
controller($router, 'get', '/inicioMonitor', 'InicioControlador@inicioMonitor');
controller($router, 'get', '/admin', 'InicioControlador@inicioAdmin');
controller($router, 'post', '/cookies/aceptar', 'InicioControlador@aceptarCookies');

//Login y registro
controller($router, 'get', '/login', 'LoginControlador@mostrarLogin');
controller($router, 'post', '/usuario/registrar', 'UsuarioControlador@registrar');
controller($router, 'get', '/confirmar-cuenta', 'UsuarioControlador@confirmarCuenta');
controller($router, 'post', '/login', 'LoginControlador@login');
controller($router, 'get', '/sesion/estado', 'LoginControlador@estadoSesion');
controller($router, 'get', '/logout', 'LoginControlador@logout');
controller($router, 'get', '/reactivar-cuenta', 'ReactivarCuentaControlador@formulario');
controller($router, 'post', '/reactivar-cuenta/solicitar', 'ReactivarCuentaControlador@solicitarCodigo');
controller($router, 'post', '/reactivar-cuenta/codigo', 'ReactivarCuentaControlador@verificarCodigo');
controller($router, 'get', '/cuenta/cambiar-clave', 'CambioClaveControlador@formulario');
controller($router, 'post', '/cuenta/cambiar-clave', 'CambioClaveControlador@guardar');
controller($router, 'get', '/cuenta/perfil', 'UsuarioControlador@formCuentaPerfil');
controller($router, 'post', '/cuenta/perfil', 'UsuarioControlador@guardarCuentaPerfil');
controller($router, 'get', '/ticket', 'RecuperarContrasenaControlador@formulario');
controller($router, 'get', '/recuperar-contrasena', 'RecuperarContrasenaControlador@formulario');
controller($router, 'get', '/no-recuerdo-mi-cuenta', 'RecuperarContrasenaControlador@formulario');
controller($router, 'get', '/no-recuerdo-quien-soy', 'RecuperarContrasenaControlador@formulario');
controller($router, 'post', '/recuperar-contrasena/email', 'RecuperarContrasenaControlador@solicitarPorEmail');
controller($router, 'post', '/ticket/crear', 'RecuperarContrasenaControlador@dniVerificarTelefono');
controller($router, 'post', '/ticket/codigo', 'RecuperarContrasenaControlador@dniVerificarCodigo');
controller($router, 'post', '/ticket/codigo-por-dni', 'RecuperarContrasenaControlador@dniTelefonoVerificarCodigo');
controller($router, 'post', '/ticket/cancelar', 'RecuperarContrasenaControlador@cancelarTicketUsuario');
controller($router, 'post', '/recuperar-contrasena/dni/telefono', 'RecuperarContrasenaControlador@dniVerificarTelefono');
controller($router, 'post', '/recuperar-contrasena/dni/codigo', 'RecuperarContrasenaControlador@dniVerificarCodigo');
controller($router, 'post', '/recuperar-contrasena/dni/codigo-por-dni', 'RecuperarContrasenaControlador@dniTelefonoVerificarCodigo');
controller($router, 'get', '/recuperar-contrasena/ticket/([a-f0-9]{64})', 'RecuperarContrasenaControlador@continuarPorTicketToken');
controller($router, 'post', '/recuperar-contrasena/dni/enviar-enlace', 'RecuperarContrasenaControlador@enviarEnlaceCorreoRevelado');
controller($router, 'get', '/restablecer-contrasena', 'RecuperarContrasenaControlador@formularioRestablecer');
controller($router, 'get', '/404', 'ErrorControlador@notFound');
controller($router, 'post', '/restablecer-contrasena', 'RecuperarContrasenaControlador@guardarNueva');

//Clientes
controller($router, 'get', '/admin/recuperacion-cuenta', 'AdminControlador@recuperacionCuentaTickets');
controller($router, 'post', '/admin/recuperacion-cuenta/enviar-correo', 'AdminControlador@enviarCorreoRecuperacionTicket');
controller($router, 'post', '/admin/recuperacion-cuenta/cerrar-ticket', 'AdminControlador@cerrarTicketRecuperacion');
controller($router, 'get', '/admin/config-seguridad', 'AdminControlador@configSeguridad');
controller($router, 'post', '/admin/config-seguridad', 'AdminControlador@guardarConfigSeguridad');
controller($router, 'get', '/admin/verClientes', 'AdminControlador@verClientes');
controller($router, 'get', '/admin/ajax/clientes', 'AdminAjaxControlador@clientes');
controller($router, 'get', '/admin/ajax/monitores', 'AdminAjaxControlador@monitores');
controller($router, 'get', '/admin/ajax/subscripciones', 'AdminAjaxControlador@subscripciones');
controller($router, 'get', '/admin/ajax/actividades', 'AdminAjaxControlador@actividades');
controller($router, 'get', '/admin/ajax/fisioterapeutas', 'AdminAjaxControlador@fisioterapeutas');
controller($router, 'get', '/admin/ajax/feedback', 'AdminAjaxControlador@feedback');
controller($router, 'get', '/admin/ajax/solicitudes', 'AdminAjaxControlador@solicitudes');
controller($router, 'get', '/admin/ajax/recuperacion-tickets-firma', 'AdminAjaxControlador@recuperacionTicketsFirma');
controller($router, 'post', '/admin/clientes/cancelar-plan', 'AdminControlador@cancelarPlanCliente');
controller($router, 'post', '/admin/clientes/bloquear', 'AdminControlador@bloquearCliente');
controller($router, 'post', '/admin/clientes/desbloquear', 'AdminControlador@desbloquearCliente');
controller($router, 'get', '/clientes/editar', 'UsuarioControlador@formEditarCliente');
controller($router, 'post', '/clientes/editar', 'UsuarioControlador@editarCliente');
controller($router, 'get', '/darse-de-baja', 'UsuarioControlador@darseDeBaja');

// Fisioterapeutas (admin)
controller($router, 'get', '/admin/fisioterapeutas', 'AdminFisioterapeutaControlador@index');
controller($router, 'get', '/admin/fisioterapeutas/nuevo', 'AdminFisioterapeutaControlador@formNuevo');
controller($router, 'post', '/admin/fisioterapeutas/nuevo', 'AdminFisioterapeutaControlador@crear');
controller($router, 'get', '/admin/fisioterapeutas/editar/(\d+)', 'AdminFisioterapeutaControlador@formEditar');
controller($router, 'post', '/admin/fisioterapeutas/editar', 'AdminFisioterapeutaControlador@guardarEditar');
controller($router, 'post', '/admin/fisioterapeutas/eliminar/(\d+)', 'AdminFisioterapeutaControlador@eliminar');

//Registrar y crear monitores
controller($router, 'get', '/admin/verMonitores', 'AdminControlador@verMonitores');
controller($router, 'get', '/admin/registrarMonitor', 'AdminControlador@registrarMonitor');
controller($router, 'post', '/admin/crearMonitor', 'AdminControlador@crearMonitor');
controller($router, 'get', '/admin/monitores/editar/(\d+)', 'AdminControlador@formEditarMonitor');
controller($router, 'post', '/admin/monitores/editar', 'AdminControlador@editarMonitor');
controller($router, 'post', '/admin/monitores/eliminar/(\d+)', 'AdminControlador@eliminarMonitor');



//Solicitudes admin
controller($router, 'get', '/admin/verSolicitudes', 'AdminControlador@verSolicitudes');
controller($router, 'get', '/admin/verSolicitudesAprobadas', 'AdminControlador@verSolicitudesAprobadas');
controller($router, 'get', '/admin/verSolicitudesRechazadas', 'AdminControlador@verSolicitudesRechazadas');
controller($router, 'post', '/admin/aprobar', 'AdminControlador@aprobarSolicitud');

//Solicitudes monitor
controller($router, 'get', '/monitor/verMonitorSolicitudes', 'MonitorControlador@verMonitorSolicitudes');
controller($router, 'get', '/monitor/formSolicitud', 'MonitorControlador@formSolicitud');
controller($router, 'post', '/monitor/crearSolicitud', 'MonitorControlador@crearSolicitud');
controller($router, 'get', '/monitor/verMisSolicitudes', 'MonitorControlador@verMisSolicitudes');

// Subscripciones
controller($router, 'get', '/admin/gestionSubscripciones', 'SubscripcionControlador@mostrarSubscripciones');
controller($router, 'get', '/admin/formSubscripcion', 'SubscripcionControlador@formSubscripcion');
controller($router, 'post', '/admin/crearSubscripcion', 'SubscripcionControlador@crearSubscripcion');
controller($router, 'get', '/admin/formEditarSubscripcion', 'SubscripcionControlador@formEditarSubscripcion');
controller($router, 'post', '/admin/editarSubscripcion', 'SubscripcionControlador@editarSubscripcion');
controller($router, 'post', '/admin/eliminarSubscripcion', 'SubscripcionControlador@eliminarSubscripcion');

//Pago
controller($router, 'get', '/pago', 'PagoControlador@index');
controller($router, 'get', '/pago/exito', 'PagoControlador@exito');
controller($router, 'get', '/pago/cancelado', 'PagoControlador@cancelado');
controller($router, 'post', '/pago/crear-intento', 'PagoControlador@crearIntentoPago');

//Salas
controller($router, 'get', '/monitor/verSalas', 'SalaControlador@index');
controller($router, 'get', '/monitor/salas/crear', 'SalaControlador@formCrearSala');
controller($router, 'post', '/monitor/salas/crear', 'SalaControlador@crear');
controller($router, 'post', '/monitor/salas/eliminar/(\d+)', 'SalaControlador@eliminar');
controller($router, 'get', '/monitor/salas/editar/(\d+)', 'SalaControlador@formEditarSala');
controller($router, 'post', '/monitor/salas/editar/(\d+)', 'SalaControlador@actualizar');

//Materiales
controller($router, 'get', '/monitor/salas/(\d+)/materiales/', 'MaterialControlador@index');
controller($router, 'get', '/monitor/salas/(\d+)/materiales', 'MaterialControlador@index');
controller($router, 'get', '/monitor/salas/(\d+)/materiales/crear', 'MaterialControlador@fromCrearMaterial');
controller($router, 'post', '/monitor/salas/(\d+)/materiales/crear', 'MaterialControlador@crear');
controller($router, 'get', '/monitor/salas/(\d+)/materiales/editar/(\d+)', 'MaterialControlador@formEditarMaterial');
controller($router, 'post', '/monitor/salas/(\d+)/materiales/editar/(\d+)', 'MaterialControlador@editar');
controller($router, 'post', '/monitor/salas/(\d+)/materiales/eliminar/(\d+)', 'MaterialControlador@eliminar');

//Actividades ADMIN
controller($router, 'get', '/admin/gestionarActividades', 'ActividadControlador@gestionarActividades');
controller($router, 'get', '/admin/actividades/crear', 'ActividadControlador@formActividad');
controller($router, 'post', '/admin/actividades/crear', 'ActividadControlador@crearActividad');
controller($router, 'get', '/admin/actividades/editar/(\d+)', 'ActividadControlador@formEditarActividad');
controller($router, 'post', '/admin/actividades/editar/(\d+)', 'ActividadControlador@editarActividad');
controller($router, 'post', '/admin/actividades/eliminar/(\d+)', 'ActividadControlador@eliminarActividad');

//Actividades Usuario
controller($router, 'get', '/usuario/actividades', 'ActividadControlador@index');


//Inscripcion
controller($router, 'post', '/usuario/inscripciones/apuntarse', 'InscripcionControlador@inscribirse');
controller($router, 'post', '/usuario/inscripciones/cancelar/', 'InscripcionControlador@cancelar');
controller($router, 'post', '/usuario/inscripciones/cancelar', 'InscripcionControlador@cancelar');
controller($router, 'get', '/usuario/inscripciones/mis-inscripciones', 'InscripcionControlador@misIncripciones');

// Fisioterapia (clientes; suscripción con fisio = S)
controller($router, 'get', '/usuario/fisio', 'FisioControlador@index');
controller($router, 'get', '/usuario/fisio/solicitar', 'FisioControlador@formSolicitar');
controller($router, 'post', '/usuario/fisio/solicitar', 'FisioControlador@solicitar');
controller($router, 'get', '/usuario/fisio/mis-citas', 'FisioControlador@misCitas');
controller($router, 'post', '/usuario/fisio/cancelar-cita', 'FisioControlador@cancelarCita');

// Panel fisioterapeuta (cuenta vinculada en admin; rol sesión fisio)
controller($router, 'get', '/fisio/citas/confirmadas', 'FisioPanelControlador@citasConfirmadas');
controller($router, 'get', '/fisio/citas', 'FisioPanelControlador@citas');
controller($router, 'get', '/fisio', 'FisioPanelControlador@inicio');

// Contacto y mensajes recibidos (admin)
controller($router, 'get', '/contacto', 'FeedbackControlador@formContacto');
controller($router, 'post', '/contacto/enviar', 'FeedbackControlador@guardar');
controller($router, 'get', '/admin/feedback', 'FeedbackControlador@verAdmin');
controller($router, 'get', '/admin/feedback/responder/(\d+)', 'FeedbackControlador@formResponder');
controller($router, 'post', '/admin/feedback/responder', 'FeedbackControlador@enviarRespuesta');
controller($router, 'post', '/admin/feedback/eliminar/(\d+)', 'FeedbackControlador@eliminar');
controller($router, 'post', '/sesion/cerrar-inactividad', 'LoginControlador@cerrarInactividad');

// Comentarios por sesión de actividad (usuarios)
controller($router, 'get', '/usuario/actividades/sesion/comentarios', 'ComentarioActividadControlador@ver');
controller($router, 'post', '/usuario/actividades/sesion/comentarios', 'ComentarioActividadControlador@guardar');
