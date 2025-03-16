<?php
require_once __DIR__ . "/app/controllers/UsuarioController.php";
require_once __DIR__ . "/app/core/Router.php";
require_once __DIR__ . "/app/controllers/AuthController.php";
require_once __DIR__ . "/app/controllers/DocenteController.php";
require_once __DIR__ . "/app/controllers/SeccionesController.php";

$router = new Router;

$router->addRoute("GET", "/users", "UsuarioController", "getAllUsers");
$router->addRoute("GET", "/users/{id}", "UsuarioController", "getOneUser");
$router->addRoute("POST", "/users", "UsuarioController", "createUser");

$router->addRoute("POST", "/login", "AuthController", "login");

$router->addRoute("GET", "/profile", "UsuarioController", "getProfile");

$router->addRoute("POST", "/docentes", "DocenteController", "createDocente");

$router->addRoute("GET", "/secciones/{id}", "SeccionesController", "getSecciones");

$router->dispatch($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"]);
?>