<?php
require_once __DIR__ . '/../controllers/DocenteController.php';

function registerDocenteRoutes($router) {
    $docenteController = new DocenteController();
    
    $router->get( "/docentes/get/{id}", [$docenteController, "getDocente"], ['Docente']);
    $router->get( "/docentes/all", [$docenteController, "getAllDocentes"], ['Docente']);
    $router->get( "/docentes/dep/{id}/jefe/{doc}", [$docenteController, "getDocentesBydepartment"], ['Docente'. 'Jefe']);
    $router->get( "/docentes/horario/sec/{id}/dep/{dep}/jefe/{jefe}", [$docenteController, "getDocentesByHorario"], ['Docente']);
    $router->get( "/docentes/usuario/{id}", [$docenteController, "getUsuarioByDocente"], ['Docente']);
    $router->get( "/docentes/get/id/{id}", [$docenteController, "getId"], ['Docente']);
    $router->post( "/docentes/video", [$docenteController, "uploadVideo"], ['Docente']);
    $router->post( "/docentes/create", [$docenteController, 'createDocente'], ['Docente']);
    $router->put( "/docentes/upload", [$docenteController, 'uploadData'], ['Docente']);
    
}

?>
