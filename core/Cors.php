<?php
require_once __DIR__ . '/../config/constants.php';

class Cors {
    public static function handle() {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            $origin = $_SERVER['HTTP_ORIGIN'];
            if (in_array($origin, ALLOWED_ORIGINS)) {
                header("Access-Control-Allow-Origin: $origin");
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Max-Age: 86400');
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH");
            }
            
            // Especifica explícitamente los headers permitidos, incluyendo tus headers personalizados
            $allowedHeaders = [
                'X-Requested-With',
                'Content-Type',
                'Authorization',
                'X-Custom-Header',
                'id',
                'seccionid',
                'estudianteid' // <- Añade aquí tus headers personalizados
            ];
            
            header("Access-Control-Allow-Headers: " . implode(', ', $allowedHeaders));
            
            exit(0);
        }
    }
}