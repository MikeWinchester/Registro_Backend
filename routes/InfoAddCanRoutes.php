<?php
require_once __DIR__ . '/../controllers/InfoAddCanController.php';

function registerInfoAddCanRoutes($router) {
    $infoNotasController = new InfoAddCanController();
    
    $router->post("/info_add_can/set", [$infoNotasController, "setFecha"]);
    $router->get("/info_add_can/get", [$infoNotasController, "getHorario"]);
}
?>