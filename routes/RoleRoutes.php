<?php
require_once __DIR__ . '/../controllers/RoleController.php';

function registerRoleRoutes($router) {
    $roleController = new RoleController();
    
    $router->get('/roles', [$roleController, 'getAll'], ['Administrador']);
    $router->get('/roles/{id}', [$roleController, 'getById'], ['Administrador']);
    $router->post('/roles', [$roleController, 'create'], ['Administrador']);
    $router->put('/roles/{id}', [$roleController, 'update'], ['Administrador']);
    $router->delete('/roles/{id}', [$roleController, 'delete'], ['Administrador']);
}