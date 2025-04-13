<?php
require_once __DIR__ . '/../controllers/DocenteController.php';

function registerDocenteRoutes($router) {
    $docenteController = new DocenteController();
    
    $router->get( "/docentes/get", [$docenteController, "getDocente"], ['Docente']);
    $router->get( "/docentes/all", [$docenteController, "getAllDocentes"], ['Docente']);
    $router->get( "/docentes/dep", [$docenteController, "getDocentesBydepartment"], ['Docente']);
    $router->get( "/docentes/horario", [$docenteController, "getDocentesByHorario"], ['Docente']);
    $router->get( "/docentes/usuario", [$docenteController, "getUsuarioByDocente"], ['Docente']);
    $router->get( "/docentes/get/id", [$docenteController, "getId"], ['Docente']);
    $router->post( "/docentes/video", [$docenteController, "uploadVideo"], ['Docente']);
    $router->post( "/docentes/create", [$docenteController, 'createDocente'], ['Docente']);
    $router->put( "/docentes/upload", [$docenteController, 'uploadVideo'], ['Docente']);
    
}

?>
