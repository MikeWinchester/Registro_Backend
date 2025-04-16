<?php
require_once __DIR__ . '/../controllers/EdificioController.php';

function registerEdificioRoutes($router) {
    $edificioController = new EdificioController();
    
    $router->get("/edificio/jefe/{id}", [$edificioController, "getEdificioByJefe"]);
}
?>