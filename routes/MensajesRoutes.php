<?php
require_once __DIR__ . '/../controllers/MensajesController.php';

function registerMensajesRoutes($router) {
    $mensajesController = new MensajesController();
    
    $router->post("/mensaje/set", [$mensajesController, "setMensaje"]);
    $router->get("/mensaje/get/rem/{id}/dest/{dest}", [$mensajesController, "getMensaje"]);
    $router->get("/mensaje/sinleer/rem/{id}/dest/{dest}", [$mensajesController, "getMensajesLeido"]);
    $router->get("/mensaje/last/user/{id}", [$mensajesController, "getInfoLastMessage"]);    
    $router->put("/mensaje/leer/rem/{id}/dest/{dest}", [$mensajesController, "leerMensaje"]);
    $router->get("/mensaje/get/last/rem/{id}/dest/{dest}", [$mensajesController, "getUltimoMensaje"]);
}