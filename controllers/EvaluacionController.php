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

    public function getEvaluaciones($request){

        $header['Docenteid'] = $request->getRouteParam(0);
        $header['Claseid'] = $request->getRouteParam(1);
        $header['Periodoacademico'] = $request->getRouteParam(2);
       
        $result = $this->evaluacion->obtenerEvaluaciones($header);
    
        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Evaluaciones encontradas correctamente", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se encontraron evaluaciones"]);
        }
    }

    private function getEvaluacionesPrivate($header){
        
        $result = $this->evaluacion->obtenerEvaluaciones($header);
    
        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Evaluaciones encontradas correctamente", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se encontraron evaluaciones"]);
        }
    }

    public function searchDoc($request){
        $header['Docenteid'] = $request->getRouteParam(0);

        $this->getEvaluacionesPrivate($header);
    }

    public function searchDocClase($request){
        $header['Docenteid'] = $request->getRouteParam(0);
        $header['Claseid'] = $request->getRouteParam(1);

        $this->getEvaluacionesPrivate($header);
    }

    public function searchClase($request){
        $header['Claseid'] = $request->getRouteParam(0);

        $this->getEvaluacionesPrivate($header);
    }

    public function searchClasePeriodo($request){
        $header['Claseid'] = $request->getRouteParam(0);
        $header['Periodoacademico'] = $request->getRouteParam(1);

        $this->getEvaluacionesPrivate($header);
    }

    public function searchPeriodo($request){
        $header['Periodoacademico'] = $request->getRouteParam(0);

        $this->getEvaluacionesPrivate($header);
    }

    public function searchDocPeriodo($request){
        $header['Docenteid'] = $request->getRouteParam(0);
        $header['Periodoacademico'] = $request->getRouteParam(1);

        $this->getEvaluacionesPrivate($header);
    }

}
?>
