<?php

require_once __DIR__ . "/../models/Seccion.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class SeccionesController {
    private $seccion;

    public function __construct() {
        $this->seccion = new Seccion();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    //Obtener Secciones por idDocente
    public function getSecciones($idDocente){

        #AuthMiddleware::authMiddleware();

        $sql = "SELECT * FROM Seccion WHERE DocenteID = ? AND PeriodoAcademico = ?";

        $result = $this->seccion->customQuery($sql, [$idDocente, $this->getPeriodo()]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }

    }

    //obtener el periodo academico actual
    private function getPeriodo(){

        $year = date("Y");
        $mon = date("n");

        $trimestre = ceil($mon / 3);
    
        return "$year-$trimestre";
    }

    
}

?>