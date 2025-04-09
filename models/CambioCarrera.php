<?php
require_once __DIR__ . "/BaseModel.php";

class CCarrera extends BaseModel {

    public function __construct() {
        parent::__construct("tbl_solicitud_cambiocarrera", 'solicitud_cambiocarrera_id');
    }

    public function obtenerSolicitudes(){
        $sql = "SELECT tbl_usuario.nombre_completo,
        tbl_usuario.numero_cuenta,     
        c_actual.nombre_carrera AS carrera_actual,     
        c_solicitada.nombre_carrera AS carrera_solicitada,     
        tbl_solicitud_cambiocarrera.fechaInscripcion,     
        tbl_solicitud_cambiocarrera.estado 
        FROM tbl_solicitud_cambiocarrera 
        JOIN tbl_estudiante ON tbl_solicitud_cambiocarrera.estudiante_id = tbl_estudiante.estudiante_id 
        JOIN tbl_usuario ON tbl_usuario.usuario_id = tbl_estudiante.usuario_id 
        JOIN tbl_carrera c_actual ON tbl_estudiante.carrera_id = c_actual.carrera_id 
        JOIN tbl_carrera c_solicitada ON tbl_solicitud_cambiocarrera.carrera_id = c_solicitada.carrera_id
        WHERE tbl_solicitud_cambiocarrera.estado = 'Pendiente'";
        
        return $this->executeWrite($sql);
    }

    public function obtenerIdEstudiante($param){
        $sql = "SELECT estudiante_id FROM tbl_estudiante WHERE usuario_id = (SELECT usuario_id FROM tbl_usuario WHERE numero_cuenta = ?)";

        return $this->fetchOne($sql, $param);
    }

    public function actualizarEstadoSolicitud($param){
        $sql = "UPDATE tbl_solicitud_cambiocarrera SET estado = ? WHERE estudiante_id = ?";

        return $this->executeWrite($sql, $param);
    }

}
?>
