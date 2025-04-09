<?php
require_once __DIR__ . '/../controllers/CambioCentroController.php';

function registerCambioCentroRoutes($router) {
    $cambioCentro = new CambioCentro();

    $router->get( "/solicitudcentro/cambio", [$cambioCentro, "getSolicitudesCentro"]);
    $router->post( "/solicitudcentro/cambio/responder", [$cambioCentro, "responderSolicitudCentro"]);
}