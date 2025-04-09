<?php
require_once __DIR__ . '/../controllers/AulaController.php';

function registerAulaRoutes($router) {
    $aulaController = new AulaController();
    
    $router->get( "/aula/get", [$aulaController, "getAulasByEdificio"]);
}
?>