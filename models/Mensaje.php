<?php
require_once __DIR__ . "/BaseModel.php";

class Mensaje extends BaseModel {


    public function __construct() {
        parent::__construct('tbl_mensajes', 'mensaje_id');
    }

    public function obtenerMensajes($param){
        $sql = "SELECT
                mensaje,
                fecha_envio,
                leido,
                CASE
                    WHEN ms.remitente_id = ? THEN 'enviado'
                    ELSE 'recibido'
                END AS tipo_mensaje
            FROM tbl_mensajes AS ms
            WHERE (ms.remitente_id = ? AND ms.destinatario_id = ?)
            OR (ms.destinatario_id = ? AND ms.remitente_id = ?)
            ORDER BY fecha_envio ASC;
            ";

        return $this->fetchAll($sql, $param);
    }

    public function leerMensajes($param){
        $sql = "UPDATE tbl_mensajes
                SET leido = 1
                WHERE (remitente_id = ? AND destinatario_id = ?)
                AND leido = 0";

        return $this->executeWrite($sql, $param);
    }

    public function obtenerCantidadMensajes($param){
        $sql = "SELECT count(1) as sin_leer
        FROM tbl_mensajes as ms
        WHERE (ms.remitente_id = ? AND ms.destinatario_id = ?)
        AND leido = 0";

        return $this->fetchOne($sql, $param);
    }

    public function obtenerUltimoMensaje($param){
        $sql = "SELECT ms.mensaje
        FROM tbl_mensajes AS ms
        INNER JOIN tbl_usuario AS us_em ON ms.remitente_id = us_em.usuario_id
        INNER JOIN tbl_usuario AS us_de ON ms.destinatario_id = us_de.usuario_id
        WHERE
            (ms.remitente_id = ? AND ms.destinatario_id = ?)
            OR (ms.remitente_id = ? AND ms.destinatario_id = ?)
        ORDER BY ms.fecha_envio DESC
        LIMIT 1";

        return $this->fetchOne($sql, $param);
    }

    public function obtenerUltimosMensajeInfo($param){
        
        $sql = "SELECT
                    ms.mensaje,
                    ms.fecha_envio,
                    us_em.nombre_completo AS remitente, 
                    us_de.nombre_completo AS destinatario
                FROM tbl_mensajes AS ms
                INNER JOIN tbl_usuario AS us_em ON ms.remitente_id = us_em.usuario_id
                INNER JOIN tbl_usuario AS us_de ON ms.destinatario_id = us_de.usuario_id
                WHERE us_em.id = ? OR us_de.id = ?
                ORDER BY ms.fecha_envio DESC
                LIMIT 3";

        return $this->fetchAll($sql, $param);
        
    }
}
?>