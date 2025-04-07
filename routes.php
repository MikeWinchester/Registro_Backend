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
require_once __DIR__ . "/app/controllers/AuthController.php";

require_once __DIR__ . "/app/controllers/ClaseController.php";
require_once __DIR__ . "/app/controllers/AulaController.php";
require_once __DIR__ . "/app/controllers/JefeController.php";
require_once __DIR__ . "/app/controllers/EstudianteController.php";
require_once __DIR__ . "/app/controllers/EsperaController.php";
require_once __DIR__ . "/app/controllers/CancelacionController.php";
require_once __DIR__ . "/app/controllers/DepartamentoController.php";
require_once __DIR__ . "/app/controllers/MensajesController.php";
require_once __DIR__ . "/app/controllers/ObservacionesController.php";

$router = new Router;

//Routes for Usuario
$router->addRoute("GET", "/users", "UsuarioController", "getAllUsers");
$router->addRoute("GET", "/users/{id}", "UsuarioController", "getOneUser");
$router->addRoute("POST", "/users", "UsuarioController", "createUser");
$router->addRoute("GET", "/profile", "UsuarioController", "getProfile");

//Routes for Auth
$router->addRoute("POST", "/login", "AuthController", "login");

//Routes for Docentes
$router->addRoute("POST", "/docentes/create", "DocenteController", "createDocente");
$router->addRoute("GET", "/docentes/get", "DocenteController", "getDocente");
$router->addRoute("GET", "/docentes/all", "DocenteController", "getAllDocentes");
$router->addRoute("POST", "/docentes/video", "DocenteController", "uploadVideo");
$router->addRoute("GET", "/docentes/dep", "DocenteController", "getDocentesBydepartment");

//Routes for Secciones
$router->addRoute("GET", "/secciones/docente/all", "SeccionesController", "getSecciones");
$router->addRoute("GET", "/secciones/docente", "SeccionesController", "getSeccionesActuales");
$router->addRoute("GET", "/secciones/get", "SeccionesController", "getSeccion");
$router->addRoute("GET", "/secciones/matricula", "SeccionesController", "getSeccionAsig");
$router->addRoute("GET", "/secciones/get/clase", "SeccionesController", "getSeccionesByClass");
$router->addRoute("GET", "/secciones/get/clase/doc", "SeccionesController", "getSeccionesByClassDoc");
$router->addRoute("GET", "/secciones/count", "SeccionesController", "getSeccionCount");
$router->addRoute("GET", "/secciones/periodo", "SeccionesController", "getPeriodoAca");
$router->addRoute("POST", "/secciones/create", "SeccionesController", "createSeccion");


//Routes for Matricula
$router->addRoute("GET", "/matricula/estudiantes", "MatriculaController", "getEstudiantesNotas");
$router->addRoute("GET", "/matricula/estudiantes/seccion", "MatriculaController", "getEstudiantes");
$router->addRoute("GET", "/matricula/get", "MatriculaController", "getMatriculaEst");
$router->addRoute("GET", "/matricula/check", "MatriculaController", "cumpleRequisito");
$router->addRoute("GET", "/matricula/horario", "MatriculaController", "cumpleHorario");
$router->addRoute("POST", "/matricula/set", "MatriculaController", "setMatricula");
$router->addRoute("DELETE", "/matricula/delete", "MatriculaController", "delMat");

//Routes for Estudiante
$router->addRoute("GET", "/estudiante/get", "EstudianteController", "getEstudiante");
$router->addRoute("GET", "/estudiante/get/cuenta", "EstudianteController", "getEstudianteByCuenta");
$router->addRoute("GET", "/estudiante/get/hist", "EstudianteController", "getHistorial");

//Routes for Admisiones
$router->addRoute("POST", "/admisiones", "AdmisionesController", "createAdmission");

//Routes for Notas
$router->addRoute("GET", "/notas/buscar", "NotasController", "searchNotas");
$router->addRoute("POST", "/notas/asignar", "NotasController", "asigNotas");

//Routes for Carreras
$router->addRoute("GET", "/carreras", "CarreraController", "getAllCareers");

//Routes for Centros
$router->addRoute("GET", "/centros", "CentroController", "getAllCenters");

//Routes for Clases
$router->addRoute("GET", "/clases", "ClaseController", "getClasesByArea");
$router->addRoute("GET", "/clases/estu", "ClaseController", "getClasesByAreaEstu");
$router->addRoute("GET", "/clases/getEdid", 'ClaseController', "getEdidByClass");
$router->addRoute("GET", "/clases/doc", "ClaseController", "getClasesAsigDoc");
$router->addRoute("POST", "/clases", "ClaseController", "createClases");

//Aula
$router->addRoute("GET", "/aula/get", "AulaController", "getAulasByEdificio");

//Jefe
$router->addRoute("GET", "/jefe/getDep", "JefeController", "getDepByJefe");
$router->addRoute("GET", "/jefe/getFac", "JefeController", "getFacByJefe");

//Lista de espera
$router->addRoute("GET", "/esp/estu", "EsperaController", "getEspByEstudiante");
$router->addRoute("GET", "/esp/count", "EsperaController", "getCupoEsperaBySec");
$router->addRoute("GET", "/esp/dep", "EsperaController", "getEstEsperaDep");
$router->addRoute("DELETE", "/esp/eliminar", "EsperaController", "delEspera");

//Lista cancelacion
$router->addRoute("GET", "/can/estu", "CancelacionController", "getCanByEstudiante");
$router->addRoute("POST", "/can/estu", "CancelacionController", "createCancelacion");

//Departamentos
$router->addRoute("GET", "/departamentos/get", "DepartamentoController", "detDeps");

//Mensajes
$router->addRoute("POST", "/mensaje/set", "MensajesController", "setMensaje");
$router->addRoute("GET", "/mensaje/get", "MensajesController", "getMensaje");
$router->addRoute("GET", "/mensaje/sinleer", "MensajesController", "getMensajesLeido");
$router->addRoute("PUT", "/mensaje/leer", "MensajesController", "leerMensaje");

//Observaciones
$router->addRoute("GET", "/observacion/get", "ObservacionesController", "getObservacion");

//Routes
$router->addRoute("GET", "/solicitud/{id}/estado", "SolicitudController", "getSolicitudEstado");

$router->dispatch($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"]);
?>
