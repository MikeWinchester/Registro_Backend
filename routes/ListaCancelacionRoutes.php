<?php
require_once __DIR__ . '/../controllers/CancelacionController.php';

function registerCancelacionRoutes($router) {
    $cancelacionController = new CancelacionController();
    
    $router->get("/can/estu/{id}", [$cancelacionController, "getCanByEstudiante"]);
    $router->post("/can/estu", [$cancelacionController, "createCancelacion"]);
    $router->get("/can/solicitud", [$cancelacionController, "getSolicitudCancel"]);
    $router->put("/can/responder", [$cancelacionController, "responderSolicitud"]);

}
?>