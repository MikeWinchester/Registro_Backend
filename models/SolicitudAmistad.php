<?php
require_once __DIR__ . "/BaseModel.php";

class SolicitudAmistad extends BaseModel {


    public function __construct() {
        parent::__construct("tbl_solicitud", 'usuario_emisor');
    }

    public function obtenerUsuariosAmigos($param){
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

        return $this->fetchAll($sql, $param);
    }

    public function obtenerUsuariosConMensajes($param){
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
                GROUP BY amigo_id, nombre_amigo";

        return $this->fetchAll($sql, $param);
    }

    public function obtenerSolicitudAmistad($param){
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

        return $this->fetchAll($param);
    }

    public function tomarDecisionSolicitud($param){
        $sql = "UPDATE tbl_solicitud_amistad 
        SET estado_solicitud_id = ? 
        WHERE (usuario_emisor ? AND usuario_receptor = ?)";

        return $this->executeWrite($sql, $param);
    }
}

?>