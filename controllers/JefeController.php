<?php
require_once __DIR__ . "/../models/Jefe.php";
require_once __DIR__ . "/BaseController.php";

class JefeController extends BaseController{
    private $jefe;
    

    public function __construct() {
        parent::__construct();
        $this->jefe = new Jefe();
    }

    public function getDepByJefe($request){
        
        $jefeID = $request->getRouteParam(0);

        $result = $this->jefe->getDepartamentoByJefe($jefeID);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }
    }


    public function getFacByJefe($request){

        $jefeID = $request->getRouteParam(0);

        $result = $this->jefe->obtenerFacultadByJefe($jefeID);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }

    }

    public function getCentroByJefe($jefe){
        
        return $this->jefe->obtenerCentroByJefe($jefe)['id'];
    }

    public function getUsuarioByJefe($request){
        
        $jefe = $request->getRouteParam(0);
        $result = $this->jefe->getUsuarioByJefe($jefe);

        if($result){
            http_response_code(200);
            echo json_encode(['message' => 'usuario obtenido', "data" => $result]);
        }else{
            http_response_code(400);
            echo json_encode(['error' => 'usuario no obtenido']);
        }
    }

    public function getId($request){
        
        $jefe = $request->getRouteParam(0);
        $result = $this->jefe->obtenerJefeId([$jefe]);

        if($result){
            http_response_code(200);
            echo json_encode(['data' => $result]);
        }else{
            http_response_code(400);
            echo json_encode(['error' => "No se pudo completa la accion"]);
        }
    }
}
?>