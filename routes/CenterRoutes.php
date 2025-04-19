<?php
require_once __DIR__ . '/../controllers/CenterController.php';

function registerCenterRoutes($router) {
    $centerController = new CenterController();
    
    $router->get('/centros', [$centerController, 'getAll']);
    $router->get('/centros/{codigo}/carreras', [$centerController, 'getCarrerasByCentro']);
}