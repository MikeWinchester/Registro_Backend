<?php
require_once __DIR__ . '/core/Cors.php';
require_once __DIR__ . '/core/Router.php';

// Habilitar CORS
Cors::handle();

// Registrar rutas
$router = new Router();

// Incluir todas las rutas
require_once __DIR__ . '/routes/api.php';

// Registrar las rutas
registerAllRoutes($router);

// Resolver la ruta
$router->resolve();
//