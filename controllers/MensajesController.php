<?php
require_once __DIR__ . "/../models/Mensaje.php";
require_once __DIR__ . "/BaseController.php";

class MensajesController extends BaseController {
    private $mensaje;
    
    public function __construct() {
        parent::__construct();
        $this->mensaje = new Mensaje();
    }

    public function setMensaje(){
        date_default_timezone_set('date_default_timezone_set("America/Tegucigalpa")');
        $data = json_decode(file_get_contents("php://input"), true);
        
        $data['fecha_envio'] =  date("Y-m-d H:i:s");
        $data['leido' ] = 0;

        $result = $this->mensaje->create($data);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "mensaje enviado"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Mensaje no enviado"]);
        }
    }

    public function getMensaje($request){

        $remid = $request->getRouteParam(0);
        $desid = $request->getRouteParam(1);

        $result = $this->mensaje->obtenerMensajes([$remid, $remid, $desid, $remid, $desid]);

        if($result){
            http_response_code(200);
            echo json_encode(["message" => "Mensajes obtenidos", "data" => $result]);
        }else{
            http_response_code(400);
            echo json_encode(["error" => "No se pudo obtener los mensajes"]);
        }
    }

    public function leerMensaje($request){
        
        $remid = $request->getRouteParam(0);
        $desid = $request->getRouteParam(1);

        $result = $this->mensaje->leerMensajes([$desid, $remid]);

        if($result){
            http_response_code(200);
            echo json_encode(["message" => "mensajes leido", "data" => $result]);
        }else{
            http_response_code(400);
            echo json_encode(["error" => "mensajes no leidos"]);
        }

    }


    public function getMensajesLeido($request){
        
        $remid = $request->getRouteParam(0);
        $desid = $request->getRouteParam(1);

        $result = $this->mensaje->obtenerCantidadMensajes([$remid, $desid]);

        if($result){
            http_response_code(200);
            echo json_encode(["message" => "mensajes obtenidos", "data" => $result]);
        }else{
            http_response_code(400);
            echo json_encode(["error" => "No se obtuvieron los mensajes"]);
        }
    }

    public function getUltimoMensaje($request){

        $emi = $request->getRouteParam(0);
        $rec = $request->getRouteParam(1);

        $result = $this->mensaje->obtenerUltimoMensaje([$emi, $rec, $rec, $emi]);

        if($result){
            http_response_code(200);
            echo json_encode(["message" => "mensajes obtenidos", "data" => $result]);
        }else{
            http_response_code(400);
            echo json_encode(["error" => "No se obtuvieron los mensajes"]);
        }
    }

    public function getInfoLastMessage($request){
        
        $estudiante = $request->getRouteParam(0);

        $result = $this->mensaje->obtenerUltimosMensajeInfo([$estudiante, $estudiante]);

        if($result){
            http_response_code(200);
            echo json_encode(['message' => 'Mensajes Obtenidos', 'data' => $result]);
        }else{
            http_response_code(400);
            echo json_encode(['error' => 'Mensajes No Obtenidos']);
        }

    }
}

?>
