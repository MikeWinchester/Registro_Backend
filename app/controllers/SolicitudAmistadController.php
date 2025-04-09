<?php
require_once __DIR__ . "/../models/SolicitudAmistad.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class SolicitudAmistadController {
    private $soli;
    
    public function __construct() {
        $this->soli = new SolicitudAmistad();
    }

    public function getUsuariosAceptadosByUsuario(){

        $header = getallheaders();

        $user = $header['usuarioid'];

        $sql = "SELECT
                    CASE
                        WHEN sa.usuario_emisor = ? THEN ur_rc.usuario_id
                        ELSE ur_em.usuario_id
                    END AS amigo_id,
                    CASE
                        WHEN sa.usuario_emisor = ? THEN ur_rc.nombre_completo
                        ELSE ur_em.nombre_completo
                    END AS nombre_amigo
                FROM tbl_solicitud_amistad AS sa
                LEFT JOIN tbl_usuario AS ur_em ON sa.usuario_emisor = ur_em.usuario_id
                LEFT JOIN tbl_usuario AS ur_rc ON sa.usuario_receptor = ur_rc.usuario_id
                WHERE (sa.usuario_emisor = ? OR sa.usuario_receptor = ?)
                AND estado_solicitud_id = 1";

        $result = $this->soli->customQuery($sql, [$user, $user, $user, $user]);

        if($result){
            http_response_code(200);
            echo json_encode(['message' => "Se ha obtenido los usuarios", 'data' => $result]);
        }else{
            http_response_code(400);
            echo json_encode(['error' => "No se ha obtenido los usuarios"]);
        }
    }

    public function getUsuariosAceptadosWithMessage(){

        $header = getallheaders();
    
        if (!isset($header['usuarioid'])) {
            http_response_code(400);
            echo json_encode(["error" => "usuarioid es requerido en el header"]);
            return;
        }
    
        $user = $header['usuarioid'];
    
        $sql = "SELECT
                    CASE
                        WHEN sa.usuario_emisor = ? THEN ur_rc.usuario_id
                        ELSE ur_em.usuario_id
                    END AS amigo_id,
                    CASE
                        WHEN sa.usuario_emisor = ? THEN ur_rc.nombre_completo
                        ELSE ur_em.nombre_completo
                    END AS nombre_amigo
                FROM tbl_solicitud_amistad AS sa
                LEFT JOIN tbl_usuario AS ur_em ON sa.usuario_emisor = ur_em.usuario_id
                LEFT JOIN tbl_usuario AS ur_rc ON sa.usuario_receptor = ur_rc.usuario_id
                INNER JOIN tbl_mensajes AS ms ON (
                    (ms.remitente_id = sa.usuario_emisor AND ms.destinatario_id = sa.usuario_receptor)
                    OR
                    (ms.remitente_id = sa.usuario_receptor AND ms.destinatario_id = sa.usuario_emisor)
                )
                WHERE (sa.usuario_emisor = ? OR sa.usuario_receptor = ?)
                AND sa.estado_solicitud_id = 1
                GROUP BY amigo_id, nombre_amigo"; // Para evitar duplicados
    
        $result = $this->soli->customQuery($sql, [$user, $user, $user, $user]);
    
        if($result){
            http_response_code(200);
            echo json_encode(['message' => "Se ha obtenido los usuarios con mensajes", 'data' => $result]);
        }else{
            http_response_code(400);
            echo json_encode(['error' => "No se ha obtenido los usuarios con mensajes"]);
        }
    }
    

    public function getUsuariosEspera(){
        $header = getallheaders();

        $user = $header['usuarioid'];

        $sql = "SELECT
                    CASE
                        WHEN sa.usuario_emisor = ? THEN ur_rc.usuario_id
                        ELSE ur_em.usuario_id
                    END AS amigo_id,
                    CASE
                        WHEN sa.usuario_emisor = ? THEN ur_rc.nombre_completo
                        ELSE ur_em.nombre_completo
                    END AS nombre_amigo
                FROM tbl_solicitud_amistad AS sa
                LEFT JOIN tbl_usuario AS ur_em ON sa.usuario_emisor = ur_em.usuario_id
                LEFT JOIN tbl_usuario AS ur_rc ON sa.usuario_receptor = ur_rc.usuario_id
                WHERE (sa.usuario_emisor = ? OR sa.usuario_receptor = ?)
                AND estado_solicitud_id = 3";

        $result = $this->soli->customQuery($sql, [$user, $user, $user, $user]);

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

        $sql = "UPDATE tbl_solicitud_amistad 
                SET estado_solicitud_id = ? 
                WHERE (usuario_emisor ? AND usuario_receptor = ?)";

        $result = $this->soli->customQueryUpdate($sql, [$data['estadoid'], $data['emisorid'], $data['receptorid']]);

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

}

?>