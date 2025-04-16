<?php
require_once __DIR__ . "/../models/SolicitudAmistad.php";
require_once __DIR__ . "/BaseController.php";


class SolicitudAmistadController extends BaseController{
    private $soli;
    
    public function __construct() {
        parent::__construct();
        $this->soli = new SolicitudAmistad();
    }

    public function getUsuariosAceptadosByUsuario($request){

        $user = $request->getRouteParam(0);

        $result = $this->soli->obtenerUsuariosAmigos([$user, $user, $user, $user, $user, $user, $user]);

        if($result){
            http_response_code(200);
            echo json_encode(['message' => "Se ha obtenido los usuarios", 'data' => $result]);
        }else{
            http_response_code(400);
            echo json_encode(['error' => "No se ha obtenido los usuarios"]);
        }
    }

    public function getUsuariosAceptadosWithMessage($request){
        
        $user = $request->getRouteParam(0);
    
        $result = $this->soli->obtenerUsuariosConMensajes([$user, $user, $user, $user, $user]);
    
        if($result){
            http_response_code(200);
            echo json_encode(['message' => "Se ha obtenido los usuarios con mensajes", 'data' => $result]);
        }else{
            http_response_code(400);
            echo json_encode(['error' => "No se ha obtenido los usuarios con mensajes"]);
        }
    }
    
    public function getUsuariosEspera($request){

        $user = $request->getRouteParam(0);

        $result = $this->soli->obtenerSolicitudEspera([$user]);

        if($result){
            http_response_code(200);
            echo json_encode(['message' => "Se ha obtenido los usuarios", 'data' => $result]);
        }else{
            http_response_code(400);
            echo json_encode(['error' => "No se ha obtenido los usuarios"]);
        }
    }

    public function updateSolicitud(){
        $data = json_decode(file_get_contents("php://input"), true);

        $result = $this->soli->tomarDecisionSolicitud([$data['estadoid'], $data['emisorid'], $data['receptorid']]);

        if($result & $data['estadoid'] == 1){
            http_response_code(200);
            echo json_encode(['message' => "Se ha aceptado al usuario"]);
        }elseif($result & $data['estadoid'] == 2){
            http_response_code(200);
            echo json_encode(['message' => "Se ha rechazado al usuario"]);
        }else{
            http_response_code(400);
            echo json_encode(['error' => "No se ha realizado la peticion"]);
        }
    }

    public function sendSolicitud(){
        date_default_timezone_set('America/Tegucigalpa');

        $data = json_decode(file_get_contents("php://input"), true);
        $data['fecha_envio'] = date('y-m-n-h-m-s');
        $data['estado_solicitud_id'] = 3;

        $result = $this->soli->checkEnvio([$data['usuario_emisor'], $data['usuario_receptor'], $data['usuario_receptor'], $data['usuario_emisor']]);

        if($result['existe'] !== 0){
            http_response_code(400);
            echo json_encode(['error' => "Ya se ha mandado solicitud"]);
            return;
        };

        $result = $this->soli->create($data);

        if(!$result){
            http_response_code(200);
            echo json_encode(['message' => "Se ha mandado la solicitud"]);
        }else{
            http_response_code(500);
            echo json_encode(['error' => "No Se ha mandado la solicitud"]);
        }
    }

}

?>