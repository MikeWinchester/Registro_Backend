<?php
require_once __DIR__ . '/../controllers/ApplicationController.php';

function registerApplicationRoutes($router) {
    $applicationController = new ApplicationController();
    
    $router->get('/solicitudes/{codigo}/estado', [$applicationController, 'getByCode']);
}