<?php
require_once __DIR__ . '/core/Cors.php';
require_once __DIR__ . '/core/Router.php';

// Habilitar CORS
Cors::handle();

// Manejo de errores
set_exception_handler(function($exception) {
    header('Content-Type: application/json');
    if (APP_DEBUG) {
        echo json_encode([
            'error' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    } else {
        echo json_encode(['error' => 'Internal Server Error']);
    }
    http_response_code($exception->getCode() ?: 500);
});

// Registrar rutas
$router = new Router();

// Incluir todas las rutas
require_once __DIR__ . '/routes/api.php';

// Registrar las rutas
registerAllRoutes($router);

// Resolver la ruta
$router->resolve();