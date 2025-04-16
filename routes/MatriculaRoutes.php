<?php
require_once __DIR__ . '/../controllers/MatriculaController.php';

function registerMatriculaRoutes($router) {
    $matriculaController = new MatriculaController();

    $router->get( "/matricula/estudiantes/{id}", [$matriculaController, "getEstudiantesNotas"], ['Estudiante', 'Jefe']);
    $router->get( "/matricula/estudiantes/seccion/{id}", [$matriculaController, "getEstudiantes"], ['Estudiante', 'Docente']);
    $router->get( "/matricula/get/{id}", [$matriculaController, "getMatriculaEst"],['Estudiante', 'Jefe']);
    $router->get( "/matricula/check/estu/{id}/clase/{id}", [$matriculaController, "cumpleRequisito"],['Estudiante', 'Jefe']);
    $router->get( "/matricula/horario", [$matriculaController, "cumpleHorario"],['Estudiante', 'Jefe']);
    $router->get( "/matricula/validate/estu/{id}", [$matriculaController, "permitirMatriculaEstu"],['Estudiante', 'Jefe']);
    $router->get( "/matricula/get/estu/{id}", [$matriculaController, "getClases"],['Estudiante', 'Jefe']);
    $router->post( "/matricula/set", [$matriculaController, "setMatricula"],['Estudiante', 'Jefe']);
    $router->delete( "/matricula/delete/est/{id}/sec/{sec}", [$matriculaController, "delMat"],['Estudiante', 'Jefe']);
}
?>

