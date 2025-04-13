<?php
require_once __DIR__ . '/../controllers/AdmisionesController.php';

function registerAdmisionesRoutes($router) {
    $admisionesController = new AdmisionesController();
    
    $router->post("/admisiones", [$admisionesController, "createAdmission"]);
}
?>