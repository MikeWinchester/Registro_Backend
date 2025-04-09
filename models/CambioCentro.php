<?php
require_once __DIR__ . "/BaseModel.php";

class CCentro extends BaseModel {

    public function __construct() {
        parent::__construct("tbl_solicitud_cambiocentro", "solicitud_cambiocentro_id");
    }

    public function obtenerSolicitudCentro(){
        $sql = "SELECT 
        tbl_usuario.nombre_completo, tbl_usuario.numero_cuenta,      
        c_actual.nombre_centro AS centro_actual,     
        c_solicitada.nombre_centro AS centro_solicitada,      
        tbl_solicitud_cambiocentro.fechaInscripcion,      
        tbl_solicitud_cambiocentro.estado  
        FROM tbl_solicitud_cambiocentro  
        JOIN tbl_estudiante ON tbl_solicitud_cambiocentro.estudiante_id = tbl_estudiante.estudiante_id  
        JOIN tbl_usuario ON tbl_usuario.usuario_id = tbl_estudiante.usuario_id  JOIN tbl_centro_regional c_actual ON tbl_estudiante.centro_regional_id = c_actual.centro_regional_id  
        JOIN tbl_centro_regional c_solicitada ON tbl_solicitud_cambiocentro.centro_regional_id = c_solicitada.centro_regional_id 
        WHERE tbl_solicitud_cambiocentro.estado = 'Pendiente'";

        return $this->fetchAll($sql);
    }

    public function obtenerIdEstudiante($param){
        $sql = "SELECT estudiante_id FROM tbl_estudiante WHERE usuario_id = (SELECT usuario_id FROM tbl_usuario WHERE numero_cuenta = ?)";

        return $this->fetchOne($sql, $param);
    }

    public function actualizarEstadoSolicitud($param){
        $sql = "UPDATE tbl_solicitud_cambiocentro  SET estado = ? WHERE estudiante_id = ?";

        return $this->executeWrite($sql, $param);
    }

}
?>
