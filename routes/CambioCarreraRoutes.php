<?php
require_once __DIR__ . '/../controllers/CambioCarreraController.php';

function registerCambioCarreraRoutes($router) {
    $cambioCarrera = new CambioCarrera();

    $router->get( "/solicitud/cambio", [$cambioCarrera, "getSolicitudes"]);
    $router->post( "/solicitud/cambio/responder", [$cambioCarrera, "responderSolicitud"]);
}