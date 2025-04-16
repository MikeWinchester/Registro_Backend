<?php
require_once __DIR__ . '/../controllers/EvaluacionController.php';

function registerEvaluacionRoutes($router) {
    $evaluacionController = new EvaluacionController();
    
    $router->get( "/evaluaciones/doc/{id}/clase/{clase}/periodo/{periodo}", [$evaluacionController, "searchNotas"]);
    $router->get( "/evaluaciones/doc/{id}", [$evaluacionController, "searchDoc"]);
    $router->get( "/evaluaciones/doc/{id}/clase/{clase}", [$evaluacionController, "searchDocClase"]);
    $router->get( "/evaluaciones/clase/{id}", [$evaluacionController, "searchClase"]);
    $router->get( "/evaluaciones/clase/{id}/periodo/{periodo}", [$evaluacionController, "searchClasePeriodo"]);
    $router->get( "/evaluaciones/periodo/{periodo}", [$evaluacionController, "searchPeriodo"]);
    $router->get( "/evaluaciones/doc/{id}/periodo/{id}", [$evaluacionController, "searchDocPeriodo"]);
}
?>