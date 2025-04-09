<?php
require_once __DIR__ . '/../controllers/CarreraController.php';

function registerCarreraRoutes($router) {
    $carreraController = new CarreraController();
    
    $router->get("/carreras", [$carreraController, "getAllCareers"]);
}
?>