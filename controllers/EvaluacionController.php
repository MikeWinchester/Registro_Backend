<?php

require_once __DIR__ . "/../models/Evaluacion.php";
require_once __DIR__ . "/BaseController.php";


class EvaluacionController extends BaseController{
    private $evaluacion;

    public function __construct() {
        parent::__construct();
        $this->evaluacion = new Evaluacion();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    public function getEvaluaciones(){
        $header = getallheaders();
        
        $result = $this->evaluacion->obtenerEvaluaciones($header);
    
        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Evaluaciones encontradas correctamente", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se encontraron evaluaciones"]);
        }
    }

}
?>
