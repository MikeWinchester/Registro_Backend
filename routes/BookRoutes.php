<?php
require_once __DIR__ . '/../controllers/BookController.php';

function registerBookRoutes($router) {
    $bookController = new BookController();
    
    $router->get('/books', [$bookController, 'listarLibros'], ['Jefe', 'Coordinador', 'Estudiante', 'Docente', 'Administrador']);
    $router->get('/books/{id}', [$bookController, 'obtenerLibro'], ['Estudiante', 'Docente', 'Administrador', 'Jefe', 'Coordinador']);
    $router->get('/books/{id}/file/{tipo}', [$bookController, 'servirArchivoLibro']);
    $router->post('/books', [$bookController, 'crearLibro'], ['Jefe', 'Coordinador']);
    $router->post('/books/{id}', [$bookController, 'actualizarLibro'], ['Jefe', 'Coordinador']);
    $router->delete('/books/{id}', [$bookController, 'eliminarLibro'], ['Jefe', 'Coordinador']);
}