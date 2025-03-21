<?php
require_once __DIR__ . "/app/controllers/UsuarioController.php";
require_once __DIR__ . "/app/core/Router.php";
require_once __DIR__ . "/app/controllers/SolicitudController.php";
require_once __DIR__ . "/app/controllers/DocenteController.php";
require_once __DIR__ . "/app/controllers/AdmisionesController.php";
require_once __DIR__ . "/app/controllers/SeccionesController.php";
require_once __DIR__ . "/app/controllers/MatriculaController.php";
require_once __DIR__ . "/app/controllers/CarreraController.php";
require_once __DIR__ . "/app/controllers/CentroController.php";
require_once __DIR__ . "/app/controllers/NotasController.php";

$router = new Router;

//Routes for Usuario
$router->addRoute("GET", "/users", "UsuarioController", "getAllUsers");
$router->addRoute("GET", "/users/{id}", "UsuarioController", "getOneUser");
$router->addRoute("POST", "/users", "UsuarioController", "createUser");
$router->addRoute("GET", "/profile", "UsuarioController", "getProfile");

//Routes for Auth
$router->addRoute("POST", "/login", "AuthController", "login");

//Routes for Docentes
$router->addRoute("POST", "/docentes", "DocenteController", "createDocente");
$router->addRoute("GET", "/docentes/{id}", "DocenteController", "getDocente");
$router->addRoute("GET", "/docentes", "DocenteController", "getAllDocentes");
$router->addRoute("POST", "/docentes/seccion", "DocenteController", "uploadVideo");

//Routes for Secciones
$router->addRoute("GET", "/secciones/docente/{idDocente}", "SeccionesController", "getSecciones");
$router->addRoute("GET", "/secciones/{idSeccion}", "SeccionesController", "getSeccion");
$router->addRoute("GET", "/secciones/matricula/", "SeccionesController", "getSeccionAsig");
$router->addRoute("GET", "/secciones/count/{idDocente}", "SeccionesController", "getSeccionCount");

//Routes for Estudiantes
$router->addRoute("GET", "/matricula/estudiantes/{id}", "MatriculaController", "getEstudiantes");

//Routes for Admisiones
$router->addRoute("POST", "/admisiones", "AdmisionesController", "createAdmission");

//Routes for Notas
$router->addRoute("POST", "/notas/asignar", "NotasController", "asigNotas");

//Routes for Carreras
$router->addRoute("GET", "/carreras", "CarreraController", "getAllCareers");

//Routes for Centros
$router->addRoute("GET", "/centros", "CentroController", "getAllCenters");

//Routes
$router->addRoute("GET", "/solicitud/{id}/estado", "SolicitudController", "getSolicitudEstado");

$router->dispatch($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"]);
?>
