<?php
require_once __DIR__ . '/../controllers/FavoriteController.php';

function registerFavoriteRoutes($router) {
    $favoriteController = new FavoriteController();
    
    $router->get('/favorites', [$favoriteController, 'listarFavoritos'], ['Estudiante', 'Docente']);
    $router->post('/favorites/toggle', [$favoriteController, 'toggleFavorito'], ['Estudiante', 'Docente']);
}