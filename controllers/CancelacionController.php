<?php

require_once __DIR__ . "/../models/Cancelacion.php";
require_once __DIR__ . "/BaseController.php";

class CancelacionController extends BaseController{
    private $cancelacion;

    public function __construct() {
        parent::__construct();
        $this->cancelacion = new Cancelacion();
        header("Content-Type: application/json");
    }

    public function getCanByEstudiante($request){

        $estudianteId = $request->getRouteParam(0);

        $result = $this->cancelacion->clasesCanceladasEstu($estudianteId);

        if ($result) {
            echo json_encode(["message" => "Secciones obtenida correctamente", 'data' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener Secciones"]);
        }
    }


    public function createCancelacion($data){

        $this->cancelacion->create($data);

        $result = $data;

        if ($result) {
            echo json_encode(["message" => "Secciones cancelada correctamente", 'data' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al cancelar Secciones"]);
        }
    }

    public function responderSolicitud(){
        $data = json_decode(file_get_contents('php://input'));

        $info = [
            $data->estado,
            $data->seccion_id,
            $data->estudiante_id
        ];
    
        $result = $this->cancelacion->createSoli($info);
    
        if ($result) {
            echo json_encode(["message" => "Solicitud contestada correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al contestar la solicitud", 'data'=>$info]);
        }
    }
    

    public function getSolicitudCancel() {

    
        $result = $this->cancelacion->obtenerSolicitudCanceladas();
    
        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Encontradas!!!!!", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No Encontradas >>>>::::::::VVVVV"]);
        }
    }

}
?>
