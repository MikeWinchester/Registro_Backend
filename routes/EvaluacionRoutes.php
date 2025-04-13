<?php
require_once __DIR__ . '/../controllers/EvaluacionController.php';

function registerEvaluacionRoutes($router) {
    $evaluacionController = new EvaluacionController();
    
    $router->get("/evaluaciones/doc", [$evaluacionController, "getEvaluaciones"]);
}
?>