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
require_once __DIR__ . "/app/controllers/InfoMatriculaController.php";

require_once __DIR__ . "/app/controllers/ClaseController.php";
require_once __DIR__ . "/app/controllers/AulaController.php";
require_once __DIR__ . "/app/controllers/JefeController.php";
require_once __DIR__ . "/app/controllers/EstudianteController.php";
require_once __DIR__ . "/app/controllers/EsperaController.php";
require_once __DIR__ . "/app/controllers/CancelacionController.php";
require_once __DIR__ . "/app/controllers/DepartamentoController.php";
require_once __DIR__ . "/app/controllers/MensajesController.php";
require_once __DIR__ . "/app/controllers/ObservacionesController.php";
require_once __DIR__ . "/app/controllers/EdificioController.php";
require_once __DIR__ . "/app/controllers/EvaluacionController.php";
require_once __DIR__ . "/app/controllers/SolicitudAmistadController.php";

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
$router->addRoute("GET", "/docentes/horario", "DocenteController", "getDocentesByHorario");
$router->addRoute("GET", "/docentes/usuario", "DocenteController", "getUsuarioByDocente");

//Routes for Secciones
$router->addRoute("GET", "/secciones/docente/all", "SeccionesController", "getSecciones");
$router->addRoute("GET", "/secciones/docente", "SeccionesController", "getSeccionesActuales");
$router->addRoute("GET", "/secciones/get", "SeccionesController", "getSeccion");
$router->addRoute("GET", "/secciones/matricula", "SeccionesController", "getSeccionAsig");
$router->addRoute("GET", "/secciones/get/clase", "SeccionesController", "getSeccionesByClass");
$router->addRoute("GET", "/secciones/get/clase/estu", "SeccionesController", "getSeccionesByClassEstu");
$router->addRoute("GET", "/secciones/get/clase/doc", "SeccionesController", "getSeccionesByClassDoc");
$router->addRoute("GET", "/secciones/count", "SeccionesController", "getSeccionCount");
$router->addRoute("GET", "/secciones/periodo", "SeccionesController", "getPeriodoAca");
$router->addRoute("GET", "/secciones/horario", "SeccionesController", "getHorarioDispo");
$router->addRoute("POST", "/secciones/create", "SeccionesController", "createSeccion");
$router->addRoute("PUT", "/secciones/update", "SeccionesController", "updateSeccion");
$router->addRoute("DELETE", "/secciones/delete", "SeccionesController", "deleteSeccion");


//Routes for Matricula
$router->addRoute("GET", "/matricula/estudiantes", "MatriculaController", "getEstudiantesNotas");
$router->addRoute("GET", "/matricula/estudiantes/seccion", "MatriculaController", "getEstudiantes");
$router->addRoute("GET", "/matricula/get", "MatriculaController", "getMatriculaEst");
$router->addRoute("GET", "/matricula/check", "MatriculaController", "cumpleRequisito");
$router->addRoute("GET", "/matricula/horario", "MatriculaController", "cumpleHorario");
$router->addRoute("GET", "/matricula/validate/estu", "MatriculaController", "permitirMatriculaEstu");
$router->addRoute("POST", "/matricula/set", "MatriculaController", "setMatricula");
$router->addRoute("DELETE", "/matricula/delete", "MatriculaController", "delMat");

//Routes for Estudiante
$router->addRoute("GET", "/estudiante/get", "EstudianteController", "getEstudiante");
$router->addRoute("GET", "/estudiante/get/cuenta", "EstudianteController", "getEstudianteByCuenta");
$router->addRoute("GET", "/estudiante/get/hist", "EstudianteController", "getHistorial");
$router->addRoute("GET", "/estudiante/usuario", "EstudianteController", "getUsuarioByEstu");

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
$router->addRoute("GET", "/jefe/usuario", "JefeController", "getUsuarioByJefe");

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
$router->addRoute("GET", "/mensaje/get/last", "MensajesController", "getUltimoMensaje");

//Observaciones
$router->addRoute("GET", "/observacion/get", "ObservacionesController", "getObservacion");

//Edificio
$router->addRoute("GET", "/edificio/jefe", "EdificioController", "getEdificioByJefe");

//Evaluaciones
$router->addRoute("GET", "/edificio/jefe", "EdificioController", "getEdificioByJefe");

//SolicituAmistad
$router->addRoute("GET", "/solicitud_amistad/get/accept", "SolicitudAmistadController", "getUsuariosAceptadosByUsuario");
$router->addRoute("GET", "/solicitud_amistad/get/waiting", "SolicitudAmistadController", "getUsuariosEspera");
$router->addRoute("GET", "/solicitud_amistad/get/update", "SolicitudAmistadController", "updateSolicitud");
$router->addRoute("GET", "/solicitud_amistad/get/message", "SolicitudAmistadController", "getUsuariosAceptadosWithMessage");

//Info Matricula
$router->addRoute("POST", "/info_matricula/set", "InfoMatriculaController", "setFecha");
$router->addRoute("GET", "/info_matricula/get", "InfoMatriculaController", "getHorario");


//Routes
$router->addRoute("GET", "/evaluaciones/doc", "EvaluacionController", "getEvaluaciones");

$router->dispatch($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"]);
?>
