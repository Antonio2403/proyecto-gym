<?php

function controller($router, $method, $route, $action)
{
    $router->$method($route, function (...$params) use ($action) {

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

//Login y registro
controller($router, 'get', '/login', 'LoginControlador@mostrarLogin');
controller($router, 'post', '/usuario/registrar', 'UsuarioControlador@registrar');
controller($router, 'get', '/confirmar-cuenta', 'UsuarioControlador@confirmarCuenta');
controller($router, 'post', '/login', 'LoginControlador@login');
controller($router, 'get', '/logout', 'LoginControlador@logout');

//Clientes
controller($router, 'get', '/admin/verClientes', 'AdminControlador@verClientes');
controller($router, 'get', '/admin/ajax/clientes', 'AdminAjaxControlador@clientes');
controller($router, 'get', '/admin/ajax/monitores', 'AdminAjaxControlador@monitores');
controller($router, 'get', '/admin/ajax/subscripciones', 'AdminAjaxControlador@subscripciones');
controller($router, 'get', '/admin/ajax/actividades', 'AdminAjaxControlador@actividades');
controller($router, 'get', '/admin/ajax/fisioterapeutas', 'AdminAjaxControlador@fisioterapeutas');
controller($router, 'get', '/admin/ajax/feedback', 'AdminAjaxControlador@feedback');
controller($router, 'get', '/admin/ajax/solicitudes', 'AdminAjaxControlador@solicitudes');
controller($router, 'get', '/clientes/editar', 'UsuarioControlador@formEditarCliente');
controller($router, 'post', '/clientes/editar', 'UsuarioControlador@editarCliente');
controller($router, 'get', '/darse-de-baja', 'UsuarioControlador@darseDeBaja');

// Fisioterapeutas (admin)
controller($router, 'get', '/admin/fisioterapeutas', 'AdminFisioterapeutaControlador@index');
controller($router, 'get', '/admin/fisioterapeutas/nuevo', 'AdminFisioterapeutaControlador@formNuevo');
controller($router, 'post', '/admin/fisioterapeutas/nuevo', 'AdminFisioterapeutaControlador@crear');
controller($router, 'get', '/admin/fisioterapeutas/editar/(\d+)', 'AdminFisioterapeutaControlador@formEditar');
controller($router, 'post', '/admin/fisioterapeutas/editar', 'AdminFisioterapeutaControlador@guardarEditar');
controller($router, 'get', '/admin/fisioterapeutas/eliminar/(\d+)', 'AdminFisioterapeutaControlador@eliminar');

//Registrar y crear monitores
controller($router, 'get', '/admin/verMonitores', 'AdminControlador@verMonitores');
controller($router, 'get', '/admin/registrarMonitor', 'AdminControlador@registrarMonitor');
controller($router, 'post', '/admin/crearMonitor', 'AdminControlador@crearMonitor');
controller($router, 'get', '/admin/monitores/editar/(\d+)', 'AdminControlador@formEditarMonitor');
controller($router, 'post', '/admin/monitores/editar', 'AdminControlador@editarMonitor');
controller($router, 'get', '/admin/monitores/eliminar/(\d+)', 'AdminControlador@eliminarMonitor');



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

//Pago
controller($router, 'get', '/pago', 'PagoControlador@index');
controller($router, 'get', '/pago/exito', 'PagoControlador@exito');
controller($router, 'get', '/pago/cancelado', 'PagoControlador@cancelado');
controller($router, 'post', '/pago/crear-intento', 'PagoControlador@crearIntentoPago');

//Salas
controller($router, 'get', '/monitor/verSalas', 'SalaControlador@index');
controller($router, 'get', '/monitor/salas/crear', 'SalaControlador@formCrearSala');
controller($router, 'post', '/monitor/salas/crear', 'SalaControlador@crear');
controller($router, 'get', '/monitor/salas/eliminar/(\d+)', 'SalaControlador@eliminar');
controller($router, 'get', '/monitor/salas/editar/(\d+)', 'SalaControlador@formEditarSala');
controller($router, 'post', '/monitor/salas/editar/(\d+)', 'SalaControlador@actualizar');

//Materiales
controller($router, 'get', '/monitor/salas/(\d+)/materiales/', 'MaterialControlador@index');
controller($router, 'get', '/monitor/salas/(\d+)/materiales', 'MaterialControlador@index');
controller($router, 'get', '/monitor/salas/(\d+)/materiales/crear', 'MaterialControlador@fromCrearMaterial');
controller($router, 'post', '/monitor/salas/(\d+)/materiales/crear', 'MaterialControlador@crear');
controller($router, 'get', '/monitor/salas/(\d+)/materiales/editar/(\d+)', 'MaterialControlador@formEditarMaterial');
controller($router, 'post', '/monitor/salas/(\d+)/materiales/editar/(\d+)', 'MaterialControlador@editar');
controller($router, 'get', '/monitor/salas/(\d+)/materiales/eliminar/(\d+)', 'MaterialControlador@eliminar');

//Actividades ADMIN
controller($router, 'get', '/admin/gestionarActividades', 'ActividadControlador@gestionarActividades');
controller($router, 'get', '/admin/actividades/crear', 'ActividadControlador@formActividad');
controller($router, 'post', '/admin/actividades/crear', 'ActividadControlador@crearActividad');
controller($router, 'get', '/admin/actividades/editar/(\d+)', 'ActividadControlador@formEditarActividad');
controller($router, 'post', '/admin/actividades/editar/(\d+)', 'ActividadControlador@editarActividad');
controller($router, 'get', '/admin/actividades/eliminar/(\d+)', 'ActividadControlador@eliminarActividad');

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
controller($router, 'get', '/admin/feedback/eliminar/(\d+)', 'FeedbackControlador@eliminar');

// Comentarios por sesión de actividad (usuarios)
controller($router, 'get', '/usuario/actividades/sesion/comentarios', 'ComentarioActividadControlador@ver');
controller($router, 'post', '/usuario/actividades/sesion/comentarios', 'ComentarioActividadControlador@guardar');
