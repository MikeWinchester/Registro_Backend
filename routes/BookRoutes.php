<?php
require_once __DIR__ . '/../controllers/BookController.php';

function registerBookRoutes($router) {
    $bookController = new BookController();
    
    $router->get('/books', [$bookController, 'listarLibros']);
    $router->get('/books/{id}', [$bookController, 'obtenerLibro']);
    $router->get('/books/{id}/file/{tipo}', [$bookController, 'servirArchivoLibro']);
}