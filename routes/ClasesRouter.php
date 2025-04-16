<?php
require_once __DIR__ . '/../controllers/ClaseController.php';

function registerClasesRouter($router) {
    $claseController = new ClaseController();
    
    $router->get( "/clases/{id}", [$claseController, "getClasesByArea"]);
    $router->get( "/clases/dep/{id}/estu/{estu}", [$claseController, "getClasesByAreaEstu"]);
    $router->get( "/clases/getEdid/{id}", [$claseController, "getEdidByClass"]);
    $router->get( "/clases/doc/{id}", [$claseController, "getClasesAsigDoc"]);
    $router->post( "/clases", [$claseController, "createClases"]);
}
?>