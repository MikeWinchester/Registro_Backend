<?php
require_once __DIR__ . '/../controllers/SavedController.php';

function registerSavedRoutes($router) {
    $savedController = new SavedController();
    
    $router->get('/saved', [$savedController, 'listarGuardados'], ['Estudiante', 'Docente']);
    $router->post('/saved/toggle', [$savedController, 'toggleGuardado'], ['Estudiante', 'Docente']);
}
?>