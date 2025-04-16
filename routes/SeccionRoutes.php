<?php
require_once __DIR__ . '/../controllers/SeccionesController.php';

function registerSeccionRoutes($router) {
    $seccionController = new SeccionesController();
    
    $router->get( "/secciones/docente/all/{id}", [$seccionController, "getSecciones"]);
    $router->get( "/secciones/docente/{id}", [$seccionController, "getSeccionesActuales"]);
    $router->get( "/secciones/get/{id}", [$seccionController, "getSeccion"]);
    $router->get( "/secciones/get/clase/{id}/jefe/{jefe}", [$seccionController, "getSeccionesByClass"]);
    $router->get( "/secciones/get/clase/{id}/estu/{est}", [$seccionController, "getSeccionesByClassEstu"]);
    $router->get( "/secciones/get/clase/{id}/doc/{doc}", [$seccionController, "getSeccionesByClassDoc"]);
    $router->get( "/secciones/count/{id}", [$seccionController, "getSeccionCount"]);
    $router->get( "/secciones/periodo", [$seccionController, "getPeriodoAca"]);
    $router->get( "/secciones/horario/dia/{dia}/aula/{aula}/doc/{id}", [$seccionController, "getHorarioDispo"]);
    $router->get( "/secciones/dep/all", [$seccionController, "getSeccionesOutParams"]);
    $router->post(  "/secciones/create", [$seccionController, "createSeccion"], ['Jefe']);
    $router->put( "/secciones/update", [$seccionController, "updateSeccion"], ['Jefe']);
    $router->delete( "/secciones/delete/{id}", [$seccionController, "deleteSeccion"], ['Jefe']);
}
?>