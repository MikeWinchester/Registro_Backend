<?php
require_once __DIR__ . '/../controllers/JefeController.php';

function registerJefeRoutes($router) {
    $jefeController = new JefeController();
    
    $router->get( "/jefe/getDep", [$jefeController, "getDepByJefe"]);
    $router->get( "/jefe/getFac", [$jefeController, "getFacByJefe"]);
    $router->get( "/jefe/usuario", [$jefeController, "getUsuarioByJefe"]);
    $router->get( "/jefe/get/id", [$jefeController, "getId"]);
}
?>
