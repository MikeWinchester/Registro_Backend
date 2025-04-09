<?php
require_once __DIR__ . '/../controllers/AuthController.php';

function registerAuthRoutes($router) {
    $authController = new AuthController();
    
    $router->post('/login', [$authController, 'login']);
    $router->post('/register', [$authController, 'register']);
    $router->get('/me', [$authController, 'me'], ['Administrador', 'Docente', 'Jefe', 'Coordinador', 'Estudiante', 'Revisor']);
}