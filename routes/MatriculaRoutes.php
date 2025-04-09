<?php
require_once __DIR__ . '/../controllers/MatriculaController.php';

function registerMatriculaRoutes($router) {
    $matriculaController = new MatriculaController();

    $router->get( "/matricula/estudiantes", [$matriculaController, "getEstudiantesNotas"]);
    $router->get( "/matricula/estudiantes/seccion", [$matriculaController, "getEstudiantes"]);
    $router->get( "/matricula/get", [$matriculaController, "getMatriculaEst"]);
    $router->get( "/matricula/check", [$matriculaController, "cumpleRequisito"]);
    $router->get( "/matricula/horario", [$matriculaController, "cumpleHorario"]);
    $router->get( "/matricula/validate/estu", [$matriculaController, "permitirMatriculaEstu"]);
    $router->post( "/matricula/set", [$matriculaController, "setMatricula"]);
    $router->delete( "/matricula/delete", [$matriculaController, "delMat"]);
}
?>

