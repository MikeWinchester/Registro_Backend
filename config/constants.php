<?php

require_once __DIR__ . '/config.php';
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'admin_registro');
define('DB_PASS', '1234');
define('DB_NAME', 'bd_registro');

// Configuración de CORS
define('ALLOWED_ORIGINS', ['http://localhost:8000', 'http://127.0.0.1:8000']);

// Configuración de JWT
define('JWT_SECRET', 'A93D1C992824DAF6D71E5C1AE345A');
define('JWT_EXPIRE', 3600); // 1 hora en segundos

// Configuración de paginación
define('DEFAULT_PER_PAGE', 10);
define('MAX_PER_PAGE', 100);

// Configuración de la aplicación
define('APP_DEBUG', true);
