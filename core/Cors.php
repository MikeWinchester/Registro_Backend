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
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH");            
            exit(0);
        }

        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-Token, X-HTTP-Method-Override, Accept, id, estudianteid, seccionid");

    }
}

//{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}