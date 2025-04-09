<?php
require_once __DIR__ . '/../controllers/DepartamentoController.php';

function registerDepartamentoRoutes($router) {
    $departamentoController = new DepartamentoController();
    
    $router->get("/departamentos/get", [$departamentoController, "detDeps"], ['Estudiante', 'Docente', 'Jefe']);
}
?>