<?php

require_once __DIR__ . "/../models/Observacion.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";


class ObservacionesController {
    private $observacion;
    

    public function __construct() {
        $this->observacion = new Observacion();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    /**
     * retorna las observaciones disponibles
     * 
     * @version 0.1.0
     */
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
