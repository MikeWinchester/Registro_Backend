<?php
require_once __DIR__ . '/../controllers/SolicitudAmistadController.php';

function registerSolicitudAmistadRoutes($router) {
    $solicitudAmistadController = new SolicitudAmistadController();
    
    $router->get("/solicitud_amistad/get/accept/{id}", [$solicitudAmistadController, "getUsuariosAceptadosByUsuario"]);
    $router->get("/solicitud_amistad/get/waiting/{id}", [$solicitudAmistadController, "getUsuariosEspera"]);
    $router->put("/solicitud_amistad/update", [$solicitudAmistadController, "updateSolicitud"]);
    $router->get("/solicitud_amistad/get/message/{id}", [$solicitudAmistadController, "getUsuariosAceptadosWithMessage"]);
    $router->post("/solicitud_amistad/set/soli", [$solicitudAmistadController, "sendSolicitud"]);
    
}
?>