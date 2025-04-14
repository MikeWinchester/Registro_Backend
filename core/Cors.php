<?php
require_once __DIR__ . '/../config/constants.php';

class Cors {
    public static function handle() {
        // Headers permitidos (personaliza según tus necesidades)
        $allowedHeaders = [
            'Content-Type',
            'Authorization',
            'X-Requested-With',
            'estudianteid',
            'id',
            'seccionid',
            'Api-Key'
        ];

        // Métodos permitidos
        $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'];

        // Manejo de ORIGIN
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            $origin = $_SERVER['HTTP_ORIGIN'];
            if (in_array($origin, ALLOWED_ORIGINS)) {
                header("Access-Control-Allow-Origin: $origin");
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Max-Age: 86400'); // Cache preflight por 24h
            }
        }

        // Manejo de preflight OPTIONS
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: " . implode(', ', $allowedMethods));
            }
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: " . implode(', ', $allowedHeaders));
            }
            
            // También puedes incluir esto para headers expuestos
            header("Access-Control-Expose-Headers: " . implode(', ', $allowedHeaders));
            
            exit(0);
        }

        // Para respuestas normales, expone los headers necesarios
        header("Access-Control-Expose-Headers: " . implode(', ', $allowedHeaders));
    }
}