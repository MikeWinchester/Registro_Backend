<?php
require_once __DIR__ . '/../controllers/ReviewerController.php';

function registerReviewerRoutes($router) {
    $reviewerController = new ReviewerController();
    
    $router->get('/revisores/{id}/solicitudes', [$reviewerController, 'getSolicitudesRevisor'], ['Revisor']);
    $router->put('/revisores/actualizar', [$reviewerController, 'actualizarSolicitud'], ['Revisor']);
    $router->get('/revisores/certificado/{id}', [$reviewerController, 'getCertificado'], ['Revisor']);
}