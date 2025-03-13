<?php
require __DIR__ . "/app/controllers/UsuarioController.php";
require __DIR__ . "/app/core/Router.php";
require __DIR__ . "/app/controllers/AuthController.php";

$router = new Router;

$router->addRoute("GET", "/", "UsuarioController", "test");
$router->addRoute("GET", "/users", "UsuarioController", "getAllUsers");
$router->addRoute("GET", "/users/{id}", "UsuarioController", "getOneUser");
$router->addRoute("POST", "/users", "UsuarioController", "createUser");

$router->addRoute("POST", "/login", "AuthController", "login");

$router->addRoute("GET", "/profile", "UsuarioController", "getProfile");

$uri = $_SERVER["REQUEST_URI"] ?? "/";
$uri = strtok($uri, "?"); // Elimina parámetros GET

$router->dispatch($_SERVER["REQUEST_METHOD"], $uri);

?>