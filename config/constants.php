<?php

require_once __DIR__ . '/config.php';
// Configuración de la base de datos
define('DB_HOST', getenv('DB_HOST'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));
define('DB_NAME', getenv('DB_NAME'));

// Configuración de CORS
define('ALLOWED_ORIGINS', explode(',', getenv('ALLOWED_ORIGINS')));

// Configuración de JWT
define('JWT_SECRET', getenv('JWT_SECRET'));
define('JWT_EXPIRE', (int)getenv('JWT_EXPIRE')); // 1 hora en segundos

// Configuración de paginación
define('DEFAULT_PER_PAGE', (int)getenv('DEFAULT_PER_PAGE')); // 10 por defecto
define('MAX_PER_PAGE', (int)getenv('MAX_PER_PAGE')); // 100 máximo

// Configuración de la aplicación
define('APP_DEBUG', filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN));

// Configuración de correo electrónico
define('MAIL_HOST', getenv('MAIL_HOST'));
define('MAIL_PORT', (int)getenv('MAIL_PORT'));
define('MAIL_USERNAME', getenv('MAIL_USERNAME'));
define('MAIL_PASSWORD', getenv('MAIL_PASSWORD'));
define('SMTP_SECURE', getenv('SMTP_SECURE'));
define('SMTP_AUTH', filter_var(getenv('SMTP_AUTH'), FILTER_VALIDATE_BOOLEAN));
define('SMTP_CHARSET', getenv('SMTP_CHARSET'));
define('SMTP_ENCODING', getenv('SMTP_ENCODING'));
define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME'));
