<?php
require_once __DIR__ . '/../controllers/EstudianteController.php';

function registerEstudianteRoutes($router) {
    $estudianteController = new EstudianteController();
    
    $router->get( "/estudiante/get", [$estudianteController, "getEstudiante"]);
    $router->get( "/estudiante/get/cuenta", [$estudianteController, "getEstudianteByCuenta"]);
    $router->get( "/estudiante/get/hist", [$estudianteController, "getHistorial"]);
    $router->get( "/estudiante/usuario", [$estudianteController, "getUsuarioByEstu"]);
    $router->get( "/estudiante/get/id", [$estudianteController, "getId"]);
    $router->get( "/estudiante/historial", [$estudianteController, "getAll"]);
    $router->put( "/estudiante/actu/desc", [$estudianteController, "updateDescripcion"]);
    $router->put( "/estudiante/upload/perfil", [$estudianteController, "uploadData"]);
    
}
?>
