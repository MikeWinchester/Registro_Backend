<?php
require_once __DIR__ . '/../controllers/NotasController.php';

function registerNotasRoutes($router) {
    $notasController = new NotasController();

    $router->get( "/notas/buscar/doc/{id}/clase/{clase}/periodo/{periodo}", [$notasController, "searchNotas"]);
    $router->get( "/notas/buscar/doc/{id}", [$notasController, "searchDoc"]);
    $router->get( "/notas/buscar/doc/{id}/clase/{clase}", [$notasController, "searchDocClase"]);
    $router->get( "/notas/buscar/clase/{id}", [$notasController, "searchClase"]);
    $router->get( "/notas/buscar/clase/{id}/periodo/{periodo}", [$notasController, "searchClasePeriodo"]);
    $router->get( "/notas/buscar/periodo/{periodo}", [$notasController, "searchPeriodo"]);
    $router->get( "/notas/buscar/doc/{id}/periodo/{id}", [$notasController, "searchDocPeriodo"]);
    $router->get( "/notas/validate", [$notasController, "permitirNotas"]);
    $router->get( "/notas/get/{id}", [$notasController, "obtenerNotas"]);
    $router->post( "/notas/asignar", [$notasController, "asigNotas"]);
    $router->post( "/notas/eva", [$notasController, "createEvaluacion"]);
    
}
?>