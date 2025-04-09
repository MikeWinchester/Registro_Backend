<?php
require_once __DIR__ . '/../controllers/MensajesController.php';

function registerMensajesRoutes($router) {
    $mensajesController = new MensajesController();
    
    $router->post("/mensaje/set", [$mensajesController, "setMensaje"]);
    $router->get("/mensaje/get", [$mensajesController, "getMensaje"]);
    $router->get("/mensaje/sinleer", [$mensajesController, "getMensajesLeido"]);
    $router->put("/mensaje/leer", [$mensajesController, "leerMensaje"]);
    $router->get("/mensaje/get/last", [$mensajesController, "getUltimoMensaje"]);
}