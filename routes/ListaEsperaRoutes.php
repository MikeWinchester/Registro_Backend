<?php
require_once __DIR__ . '/../controllers/EsperaController.php';

function registerEsperaRoutes($router) {
    $esperaController = new EsperaController();
    
    $router->get( "/esp/estu/{id}", [$esperaController, "getEspByEstudiante"]);
    $router->get( "/esp/count/{id}", [$esperaController, "getCupoEsperaBySec"]);
    $router->get( "/esp/dep/{id}", [$esperaController, "getEstEsperaDep"]);
    $router->delete( "/esp/eliminar/sec/{id}/est/{est}", [$esperaController, "delEspera"]);
}
?>