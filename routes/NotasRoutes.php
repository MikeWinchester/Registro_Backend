<?php
require_once __DIR__ . '/../controllers/NotasController.php';

function registerNotasRoutes($router) {
    $notasController = new NotasController();

    $router->get( "/notas/buscar", [$notasController, "searchNotas"]);
    $router->get( "/notas/validate", [$notasController, "permitirNotas"]);
    $router->post( "/notas/asignar", [$notasController, "asigNotas"]);
}
?>