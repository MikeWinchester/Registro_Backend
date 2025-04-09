<?php
require_once __DIR__ . "/../models/Mensaje.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class MensajesController {
    private $mensaje;
    
    public function __construct() {
        $this->mensaje = new Mensaje();
    }


    /**
     * Crea un mensaje entre usuarios
     *
     * @version 0.1.o
     */
    public function setMensaje(){

        #AuthMiddleware::authMiddleware();

        $data = json_decode(file_get_contents("php://input"), true);
        
        $result = $this->mensaje->create($data);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "mensaje enviado"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Mensaje no enviado"]);
        }
    }

    /**
     * Obtiene los mensajes entre dos usuario
     * 
     * @version 0.1.0
     */
    public function getMensaje(){

        $header = getallheaders();

        if(!isset($header['remitenteid']) && !isset($header['destinatarioid'])){
            http_response_code(400);
            echo json_encode(["error" => "remitenteid y destinatarioid es requerido en el header"]);
            return;
        }

        $remid = $header['remitenteid'];
        $desid = $header['destinatarioid'];

        $sql = "SELECT mensaje, fecha_envio, leido
                FROM tbl_mensajes as ms
                WHERE (ms.remitente_id = ? AND ms.destinatario_id = ?)
                OR (ms.destinatario_id = ? AND ms.remitente_id = ?)";

        $result = $this->mensaje->customQuery($sql, [$remid, $desid, $remid, $desid]);

        if($result){
            http_response_code(200);
            echo json_encode(["message" => "mensajes obtenidos", "data" => $result]);
        }else{
            http_response_code(400);
            echo json_encode(["error" => "No se obtuvieron los mensajes"]);
        }
    }

    /**
     * Marcar como leido el mensaje
     * 
     * @version 0.1.0
     */
    public function leerMensaje(){
        $header = getallheaders();

        if(!isset($header['remitenteid']) && !isset($header['destinatarioid'])){
            http_response_code(400);
            echo json_encode(["error" => "remitenteid y destinatarioid es requerido en el header"]);
            return;
        }

        $remid = $header['remitenteid'];
        $desid = $header['destinatarioid'];

        $sql = "UPDATE tbl_mensajes
                SET leido = 1
                WHERE (remitente_id = ? AND destinatario_id = ?)
                AND leido = 0";

        $result = $this->mensaje->customQueryUpdate($sql, [$desid, $remid]);

        if($result){
            http_response_code(200);
            echo json_encode(["message" => "mensajes leido", "data" => $result]);
        }else{
            http_response_code(400);
            echo json_encode(["error" => "mensajes no leidos"]);
        }

    }

    /**
     * Obtiene la cantidad de mensajes sin leer
     * 
     * @version 0.1.0
     */
    public function getMensajesLeido(){
        $header = getallheaders();

        if(!isset($header['remitenteid']) && !isset($header['destinatarioid'])){
            http_response_code(400);
            echo json_encode(["error" => "remitenteid y destinatarioid es requerido en el header"]);
            return;
        }

        
        $remid = $header['remitenteid'];
        $desid = $header['destinatarioid'];

        $sql = "SELECT count(1) as sin_leer
                FROM tbl_mensajes as ms
                WHERE (ms.remitente_id = ? AND ms.destinatario_id = ?)
                AND leido = 0";

        $result = $this->mensaje->customQuery($sql, [$remid, $desid]);

        if($result){
            http_response_code(200);
            echo json_encode(["message" => "mensajes obtenidos", "data" => $result]);
        }else{
            http_response_code(400);
            echo json_encode(["error" => "No se obtuvieron los mensajes"]);
        }
    }

    public function getUltimoMensaje(){
        $header = getallheaders();

        $emi = $header['emisorid'];
        $rec = $header['receptorid'];

        $sql = "SELECT ms.mensaje
                FROM tbl_mensajes AS ms
                INNER JOIN tbl_usuario AS us_em ON ms.remitente_id = us_em.usuario_id
                INNER JOIN tbl_usuario AS us_de ON ms.destinatario_id = us_de.usuario_id
                WHERE
                    (ms.remitente_id = ? AND ms.destinatario_id = ?)
                    OR (ms.remitente_id = ? AND ms.destinatario_id = ?)
                ORDER BY ms.fecha_envio DESC
                LIMIT 1";

        $result = $this->mensaje->customQuery($sql, [$emi, $rec, $rec, $emi]);

        if($result){
            http_response_code(200);
            echo json_encode(["message" => "mensajes obtenidos", "data" => $result]);
        }else{
            http_response_code(400);
            echo json_encode(["error" => "No se obtuvieron los mensajes"]);
        }
    }
}

?>
