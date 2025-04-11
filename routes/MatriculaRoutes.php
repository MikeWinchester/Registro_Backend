<?php
require_once __DIR__ . '/../controllers/MatriculaController.php';

function registerMatriculaRoutes($router) {
    $matriculaController = new MatriculaController();

    $router->get( "/matricula/estudiantes", [$matriculaController, "getEstudiantesNotas"], ['Estudiante', 'Jefe']);
    $router->get( "/matricula/estudiantes/seccion", [$matriculaController, "getEstudiantes"], ['Estudiante', 'Docente']);
    $router->get( "/matricula/get", [$matriculaController, "getMatriculaEst"],['Estudiante', 'Jefe']);
    $router->get( "/matricula/check", [$matriculaController, "cumpleRequisito"],['Estudiante', 'Jefe']);
    $router->get( "/matricula/horario", [$matriculaController, "cumpleHorario"],['Estudiante', 'Jefe']);
    $router->get( "/matricula/validate/estu", [$matriculaController, "permitirMatriculaEstu"],['Estudiante', 'Jefe']);
    $router->post( "/matricula/set", [$matriculaController, "setMatricula"],['Estudiante', 'Jefe']);
    $router->delete( "/matricula/delete", [$matriculaController, "delMat"],['Estudiante', 'Jefe']);
}
?>

