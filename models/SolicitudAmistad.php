<?php
require_once __DIR__ . "/BaseModel.php";

class SolicitudAmistad extends BaseModel {


    public function __construct() {
        parent::__construct("tbl_solicitud_amistad", 'usuario_emisor');
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
                    END AS nombre_amigo,

                    CASE
                        WHEN sa.usuario_emisor = ? THEN et_rc.foto_perfil
                        ELSE et_em.foto_perfil
                    END AS foto_perfil,

                    CASE
                        WHEN sa.usuario_emisor = ? THEN cr_rc.nombre_carrera
                        ELSE cr_em.nombre_carrera
                    END AS nombre_carrera,

                    CASE
                        WHEN sa.usuario_emisor = ? THEN ur_rc.numero_cuenta
                        ELSE ur_em.numero_cuenta
                    END AS numero_cuenta

                FROM tbl_solicitud_amistad AS sa

                LEFT JOIN tbl_usuario AS ur_em ON sa.usuario_emisor = ur_em.usuario_id
                LEFT JOIN tbl_usuario AS ur_rc ON sa.usuario_receptor = ur_rc.usuario_id

                LEFT JOIN tbl_estudiante AS et_em ON ur_em.usuario_id = et_em.usuario_id
                LEFT JOIN tbl_estudiante AS et_rc ON ur_rc.usuario_id = et_rc.usuario_id

                LEFT JOIN tbl_carrera AS cr_em ON et_em.carrera_id = cr_em.carrera_id
                LEFT JOIN tbl_carrera AS cr_rc ON et_rc.carrera_id = cr_rc.carrera_id

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
                    END AS nombre_amigo,

                    CASE
                        WHEN sa.usuario_emisor = ? THEN et_rc.foto_perfil
                        ELSE et_em.foto_perfil
                    END AS foto_perfil

                FROM tbl_solicitud_amistad AS sa

                LEFT JOIN tbl_usuario AS ur_em ON sa.usuario_emisor = ur_em.usuario_id
                LEFT JOIN tbl_usuario AS ur_rc ON sa.usuario_receptor = ur_rc.usuario_id

                LEFT JOIN tbl_estudiante AS et_em ON ur_em.usuario_id = et_em.usuario_id
                LEFT JOIN tbl_estudiante AS et_rc ON ur_rc.usuario_id = et_rc.usuario_id

                INNER JOIN tbl_mensajes AS ms ON (
                    (ms.remitente_id = sa.usuario_emisor AND ms.destinatario_id = sa.usuario_receptor)
                    OR
                    (ms.remitente_id = sa.usuario_receptor AND ms.destinatario_id = sa.usuario_emisor)
                )

                WHERE (sa.usuario_emisor = ? OR sa.usuario_receptor = ?)
                AND sa.estado_solicitud_id = 1

                GROUP BY amigo_id, nombre_amigo, foto_perfil";

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
                    END AS nombre_amigo,

                    CASE
                        WHEN sa.usuario_emisor = ? THEN ur_rc.numero_cuenta
                        ELSE ur_em.numero_cuenta
                    END AS numero_cuenta,

                    CASE
                        WHEN sa.usuario_emisor = ? THEN ca_rc.nombre_carrera
                        ELSE ca_em.nombre_carrera
                    END AS nombre_carrera,

                    sa.fecha_envio

                FROM tbl_solicitud_amistad AS sa
                LEFT JOIN tbl_usuario AS ur_em ON sa.usuario_emisor = ur_em.usuario_id
                LEFT JOIN tbl_usuario AS ur_rc ON sa.usuario_receptor = ur_rc.usuario_id
                LEFT JOIN tbl_estudiante AS et_em ON ur_em.usuario_id = et_em.usuario_id
                LEFT JOIN tbl_estudiante AS et_rc ON ur_rc.usuario_id = et_rc.usuario_id
                LEFT JOIN tbl_carrera AS ca_em ON et_em.carrera_id = ca_em.carrera_id
                LEFT JOIN tbl_carrera AS ca_rc ON et_rc.carrera_id = ca_rc.carrera_id
                WHERE (sa.usuario_emisor = ? OR sa.usuario_receptor = ?)
                AND estado_solicitud_id = 3";

        return $this->fetchAll($sql,$param);
    }

    public function obtenerSolicitudEspera($param){
        $sql = "SELECT
                    ur_em.usuario_id AS amigo_id,
                    ur_em.nombre_completo AS nombre_amigo,
                    ur_em.numero_cuenta AS numero_cuenta,
                    ca_em.nombre_carrera AS nombre_carrera,
                    sa.fecha_envio

                FROM tbl_solicitud_amistad AS sa
                LEFT JOIN tbl_usuario AS ur_em ON sa.usuario_emisor = ur_em.usuario_id
                LEFT JOIN tbl_estudiante AS et_em ON ur_em.usuario_id = et_em.usuario_id
                LEFT JOIN tbl_carrera AS ca_em ON et_em.carrera_id = ca_em.carrera_id

                WHERE sa.usuario_receptor = ?
                AND sa.estado_solicitud_id = 3";

        return $this->fetchAll($sql,$param);
    }
    

    public function tomarDecisionSolicitud($param){
        $sql = "UPDATE tbl_solicitud_amistad
        SET estado_solicitud_id = ?
        WHERE (usuario_emisor = ? AND usuario_receptor = ?)";

        return $this->executeWrite($sql, $param);
    }

    public function obtenerId($usr){
        $sql = "SELECT usuario_id AS id
                FROM tbl_estudiante
                WHERE id = ?";

        return $this->fetchOne($sql, $usr);
    }

    public function checkEnvio($param){
        $sql = "SELECT count(1) as existe
                FROM tbl_solicitud_amistad
                WHERE (usuario_emisor = ? AND usuario_receptor = ?)
                OR (usuario_emisor = ? AND usuario_receptor = ?)";
        
        return $this->fetchOne($sql, $param);
    }
}


?>