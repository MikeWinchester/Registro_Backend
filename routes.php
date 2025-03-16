<?php
require_once __DIR__ . "/app/controllers/UsuarioController.php";
require_once __DIR__ . "/app/core/Router.php";
require_once __DIR__ . "/app/controllers/AuthController.php";
require_once __DIR__ . "/app/controllers/DocenteController.php";
require_once __DIR__ . "/app/controllers/SeccionesController.php";
require_once __DIR__ . "/app/controllers/MatriculaController.php";

$router = new Router;

$router->addRoute("GET", "/users", "UsuarioController", "getAllUsers");
$router->addRoute("GET", "/users/{id}", "UsuarioController", "getOneUser");
$router->addRoute("POST", "/users", "UsuarioController", "createUser");

$router->addRoute("POST", "/login", "AuthController", "login");

$router->addRoute("GET", "/profile", "UsuarioController", "getProfile");

$router->addRoute("POST", "/docentes", "DocenteController", "createDocente");
$router->addRoute("GET", "/docentes/{id}", "DocenteController", "getDocente");
$router->addRoute("GET", "/docentes", "DocenteController", "getAllDocentes");
$router->addRoute("POST", "/docentes/seccion", "DocenteController", "uploadVideo");

$router->addRoute("GET", "/secciones/docente/{id}", "SeccionesController", "getSecciones");
$router->addRoute("GET", "/secciones/{id}", "SeccionesController", "getSeccion");
$router->addRoute("GET", "/secciones/matricula/", "SeccionesController", "getSeccionAsig");

$router->addRoute("GET", "/matricula/estudiantes/{id}", "MatriculaController", "getEstudiantes");

$router->dispatch($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"]);
?>