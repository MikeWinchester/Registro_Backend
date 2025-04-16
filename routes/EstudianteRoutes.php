<?php
require_once __DIR__ . '/../controllers/EstudianteController.php';

function registerEstudianteRoutes($router) {
    $estudianteController = new EstudianteController();
    
    $router->get( "/estudiante/get/{id}", [$estudianteController, "getEstudiante"]);
    $router->get( "/estudiante/get/cuenta/{id}", [$estudianteController, "getEstudianteByCuenta"]);
    $router->get( "/estudiante/get/hist/{id}", [$estudianteController, "getHistorial"]);
    $router->get( "/estudiante/usuario/{id}", [$estudianteController, "getUsuarioByEstu"]);
    $router->get( "/estudiante/get/id/{id}", [$estudianteController, "getId"]);
    $router->get( "/estudiante/historial", [$estudianteController, "getAll"]);
    $router->get( "/estudiante/get/galeria/{id}", [$estudianteController, "getGaleriaEstu"]);
    $router->get( "/estudiante/get/hist/id/{id}", [$estudianteController, "getHistorialById"]);
    $router->get( "/estudiante/get/indices/{id}", [$estudianteController, "getIndicesById"]);
    $router->get( "/estudiante/get/last/clases/{id}", [$estudianteController, "getLastClass"]);
    $router->put( "/estudiante/actu/desc", [$estudianteController, "updateDescripcion"]);
    $router->put( "/estudiante/upload/perfil", [$estudianteController, "uploadData"]);
    $router->post( "/estudiante/upload/galeria", [$estudianteController, "uploadGaleria"]);
    $router->delete( "/estudiante/delete/galeria", [$estudianteController, "deleteFotoGal"]);
    
    
}
?>
