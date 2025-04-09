<?php
require_once __DIR__ . '/../controllers/SolicitudAmistadController.php';

function registerSolicitudAmistadRoutes($router) {
    $solicitudAmistadController = new SolicitudAmistadController();
    
    $router->get("/solicitud_amistad/get/accept", [$solicitudAmistadController, "getUsuariosAceptadosByUsuario"]);
    $router->get("/solicitud_amistad/get/waiting", [$solicitudAmistadController, "getUsuariosEspera"]);
    $router->get("/solicitud_amistad/get/update", [$solicitudAmistadController, "updateSolicitud"]);
    $router->get("/solicitud_amistad/get/message", [$solicitudAmistadController, "getUsuariosAceptadosWithMessage"]);
}
?>