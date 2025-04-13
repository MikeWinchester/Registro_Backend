<?php
require_once __DIR__ . '/../controllers/InfoMatriculaController.php';

function registerInfoMatriculaRoutes($router) {
    $infoMatriculaController = new InfoMatriculaController();
    
    $router->post("/info_matricula/set", [$infoMatriculaController, "setFecha"]);
    $router->get("/info_matricula/get", [$infoMatriculaController, "getHorario"]);
}
?>