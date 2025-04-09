<?php
require_once __DIR__ . '/../controllers/UserController.php';

function registerUserRoutes($router) {
    $userController = new UserController();
    
    $router->get('/users', [$userController, 'getAll']);
    $router->get('/users/{id}', [$userController, 'getById'], ['Administrador']);
    $router->post('/users', [$userController, 'create'], ['Administrador']);
    $router->put('/users/{id}', [$userController, 'update'], ['Administrador']);
    $router->delete('/users/{id}', [$userController, 'delete'], ['Administrador']);
    
    $router->get('/users/{id}/roles', [$userController, 'getUserRoles'], ['Administrador']);
    $router->post('/users/{id}/roles', [$userController, 'assignRole'], ['Administrador']);
    $router->delete('/users/{id}/roles/{roleId}', [$userController, 'removeRole'], ['Administrador']);
}
?>