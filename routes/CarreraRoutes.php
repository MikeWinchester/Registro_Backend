<?php
require_once __DIR__ . '/../controllers/CareerController.php';

function registerCarreraRoutes($router) {
    $carreraController = new CareerController();
    
    $router->get("/carreras", [$carreraController, "getAllCareers"]);
}
?>