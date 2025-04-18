<?php

require_once __DIR__ . "/../models/Espera.php";
require_once __DIR__ . "/BaseController.php";

class EsperaController extends BaseController {
    private $espera;

    public function __construct() {
        parent::__construct();
        $this->espera = new Espera();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    public function getEspByEstudiante($request){

        $estudianteId = $request->getRouteParam(0);

        $result = $this->espera->obtenerListaEsperaByEstu($estudianteId);

        if ($result) {
            echo json_encode(["message" => "Secciones obtenida correctamente", 'data' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener Secciones"]);
        }
    }


    public function getCupoEsperaBySec($request){
        
        $seccionId = $request->getRouteParam(0);

        $result = $this->espera->obtenerCuposEspera($seccionId);

        if ($result) {
            echo json_encode(["message" => "Secciones obtenidas correctamente", "data" => $result]);
        } else {
            echo json_encode(["message" => "Secciones en espera inicia", "data" => [['en_espera' => 0]]]);
        }
    }
    

    public function delEspera($request){

        $seccionId = $request->getRouteParam(0);
        $estudianteId = $request->getRouteParam(1);

        $result = $this->espera->eliminarEspera($seccionId, $estudianteId);

        if ($result) {
            echo json_encode(["message" => "Secciones obtenida correctamente", 'data' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener Secciones"]);
        }
    }

    public function getEstEsperaDep($request){
        
        $departamentoid = $request->getRouteParam(0);

        $result = $this->espera->obtenerEsperaByDep($departamentoid);

        if ($result) {
            echo json_encode(["message" => "Lista de espera obtenida correctamente", "data" => $result]);
        } else {
            echo json_encode(["message" => "Lista de espera no obtenida"]);
        }
    }
    
}
?>
