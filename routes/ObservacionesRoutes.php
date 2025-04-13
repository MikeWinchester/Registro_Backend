<?php
require_once __DIR__ . '/../controllers/ObservacionesController.php';

function registerObservacionesRoutes($router) {
    $observacionesController = new ObservacionesController();
    
    $router->get("/observacion/get", [$observacionesController, "getObservacion"]);
}