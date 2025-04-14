<?php
require_once __DIR__ . '/../controllers/NotasController.php';

function registerNotasRoutes($router) {
    $notasController = new NotasController();

    $router->get( "/notas/buscar", [$notasController, "searchNotas"]);
    $router->get( "/notas/validate", [$notasController, "permitirNotas"]);
    $router->get( "/notas/get/{id}", [$notasController, "obtenerNotas"]);
    $router->post( "/notas/asignar", [$notasController, "asigNotas"]);
    $router->post( "/notas/eva", [$notasController, "createEvaluacion"]);
    
}
?>