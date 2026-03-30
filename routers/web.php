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
controller($router, 'get', '/inicioAdmin', 'InicioControlador@inicioAdmin');
controller($router, 'get', '/inicioUsuario', 'InicioControlador@inicioUsuario');
controller($router, 'get', '/inicioMonitor', 'InicioControlador@inicioMonitor');

//Login y registro
controller($router, 'get', '/login', 'LoginControlador@mostrarLogin');
controller($router, 'post', '/usuario/registrar', 'UsuarioControlador@registrar');
controller($router, 'post', '/login', 'LoginControlador@login');
controller($router, 'get', '/logout', 'LoginControlador@logout');

//Registrar y crear monitores
controller($router, 'get', '/admin/registrarMonitor', 'AdminControlador@registrarMonitor');
controller($router, 'post', '/admin/crearMonitor', 'AdminControlador@crearMonitor');

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

//Pago
controller($router, 'get', '/pago', 'PagoControlador@index');
controller($router, 'post', '/pago/crear-sesion', 'PagoControlador@crearSesion');
controller($router, 'get', '/pago/exito', 'PagoControlador@exito');
controller($router, 'get', '/pago/cancelado', 'PagoControlador@cancelado');

controller($router, 'post', '/pago/crear-intento', 'PagoControlador@crearIntentoPago');
