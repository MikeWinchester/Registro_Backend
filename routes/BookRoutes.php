<?php
require_once __DIR__ . '/../controllers/BookController.php';

function registerBookRoutes($router) {
    $bookController = new BookController();
    
    $router->get('/books', [$bookController, 'listarLibros']);
    $router->get('/books/{id}', [$bookController, 'obtenerLibro']);
    $router->get('/books/{id}/file/{tipo}', [$bookController, 'servirArchivoLibro']);
    $router->post('/books', [$bookController, 'crearLibro'], ['Jefe', 'Coordinador']);
    $router->post('/books/{id}', [$bookController, 'actualizarLibro'], ['Jefe', 'Coordinador']);
    $router->delete('/books/{id}', [$bookController, 'eliminarLibro'], ['Jefe', 'Coordinador']);
}