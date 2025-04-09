<?php
require_once __DIR__ . '/../controllers/SeccionesController.php';

function registerSeccionRoutes($router) {
    $seccionController = new SeccionesController();
    
    $router->get( "/secciones/docente/all", [$seccionController, "getSecciones"]);
    $router->get( "/secciones/docente", [$seccionController, "getSeccionesActuales"]);
    $router->get( "/secciones/get", [$seccionController, "getSeccion"]);
    $router->get( "/secciones/matricula", [$seccionController, "getSeccionAsig"]);
    $router->get( "/secciones/get/clase", [$seccionController, "getSeccionesByClass"]);
    $router->get( "/secciones/get/clase/estu", [$seccionController, "getSeccionesByClassEstu"]);
    $router->get( "/secciones/get/clase/doc", [$seccionController, "getSeccionesByClassDoc"]);
    $router->get( "/secciones/count", [$seccionController, "getSeccionCount"]);
    $router->get( "/secciones/periodo", [$seccionController, "getPeriodoAca"]);
    $router->get( "/secciones/horario", [$seccionController, "getHorarioDispo"]);
    $router->post(  "/secciones/create", [$seccionController, "createSeccion"], ['Jefe']);
    $router->put( "/secciones/update", [$seccionController, "updateSeccion"], ['Jefe']);
    $router->delete( "/secciones/delete", [$seccionController, "deleteSeccion"], ['Jefe']);
}
?>