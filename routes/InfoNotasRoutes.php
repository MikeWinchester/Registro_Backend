<?php
require_once __DIR__ . '/../controllers/InfoNotasControllerr.php';

function registerInfoNotasRoutes($router) {
    $infoNotasController = new InfoNotasControllerr();
    
    $router->post("/info_notas/set", [$infoNotasController, "setFecha"]);
    $router->get("/info_notas/get", [$infoNotasController, "getHorario"]);
}
?>