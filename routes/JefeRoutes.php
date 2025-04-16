<?php
require_once __DIR__ . '/../controllers/JefeController.php';

function registerJefeRoutes($router) {
    $jefeController = new JefeController();
    
    $router->get( "/jefe/getDep/{id}", [$jefeController, "getDepByJefe"]);
    $router->get( "/jefe/getFac/{id}", [$jefeController, "getFacByJefe"]);
    $router->get( "/jefe/usuario/{id}", [$jefeController, "getUsuarioByJefe"]);
    $router->get( "/jefe/get/id/{id}", [$jefeController, "getId"]);
}
?>
