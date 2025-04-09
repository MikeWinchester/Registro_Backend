<?php
require_once __DIR__ . '/../controllers/EsperaController.php';

function registerEsperaRoutes($router) {
    $esperaController = new EsperaController();
    
    $router->get( "/esp/estu", [$esperaController, "getEspByEstudiante"]);
    $router->get( "/esp/count", [$esperaController, "getCupoEsperaBySec"]);
    $router->get( "/esp/dep", [$esperaController, "getEstEsperaDep"]);
    $router->delete( "/esp/eliminar", [$esperaController, "delEspera"]);
}
?>