<?php
require_once __DIR__ . "/app/controllers/UsuarioController.php";
require_once __DIR__ . "/app/core/Router.php";
require_once __DIR__ . "/app/controllers/AuthController.php";

$host = "https://api-registro-faeaeyafg8fdfwdj.centralus-01.azurewebsites.net";


$router = new Router;

$router->addRoute("GET", `https://api-registro-faeaeyafg8fdfwdj.centralus-01.azurewebsites.net/users`, "UsuarioController", "getAllUsers");
$router->addRoute("GET", `{$host}/users/{id}`, "UsuarioController", "getOneUser");
$router->addRoute("POST", `{$host}/users`, "UsuarioController", "createUser");

$router->addRoute("POST", `{$host}/login`, "AuthController", "login");

$router->addRoute("GET", `{$host}/profile`, "UsuarioController", "getProfile");

$router->dispatch($_SERVER["REQUEST_METHOD"], explode("?", $_SERVER["REQUEST_URI"])[0]);
?>