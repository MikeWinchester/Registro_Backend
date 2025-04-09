<?php

require_once __DIR__ . "/../models/Observacion.php";
require_once __DIR__ . "/BaseController.php";


class ObservacionesController extends BaseController{
    private $observacion;
    

    public function __construct() {
        $this->observacion = new Observacion();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    public function getObservacion(){

    
        $result = $this->observacion->getAll();

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Aula obtenida correctamente", 'data' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener Aula"]);
        }
    
    }

}
?>
