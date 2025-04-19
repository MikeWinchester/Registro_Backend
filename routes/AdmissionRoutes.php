<?php
require_once __DIR__ . '/../controllers/AdmissionController.php';

function registerAdmissionRoutes($router) {
    $admissionController = new AdmissionController();
    
    $router->post('/admisiones', [$admissionController, 'create']);
}