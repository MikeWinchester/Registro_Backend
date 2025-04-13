<?php
require_once __DIR__ . '/../controllers/CentroController.php';

function registerCentroRoutes($router) {
    $centroController = new CentroController();
    
    $router->get("/centros", [$centroController, "getAllCenters"]);
}

?>
