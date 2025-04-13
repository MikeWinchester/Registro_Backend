<?php
require_once __DIR__ . '/../controllers/ClaseController.php';

function registerClasesRouter($router) {
    $claseController = new ClaseController();
    
    $router->get( "/clases", [$claseController, "getClasesByArea"]);
    $router->get( "/clases/estu", [$claseController, "getClasesByAreaEstu"]);
    $router->get( "/clases/getEdid", [$claseController, "getEdidByClass"]);
    $router->get( "/clases/doc", [$claseController, "getClasesAsigDoc"]);
    $router->post( "/clases", [$claseController, "createClases"]);
}
?>