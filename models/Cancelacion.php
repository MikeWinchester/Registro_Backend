<?php
require_once __DIR__ . "/BaseModel.php";

class Cancelacion extends BaseModel {
    
    public function __construct() {
        parent::__construct('tbl_lista_cancelacion', 'lista_cancelacion_id');
    }

    public function clasesCanceladasEstu($estudianteid){
        
        $sql = 'SELECT DISTINCT cn.seccion_id, cl.nombre ,periodo_academico, aula, horario, cupo_maximo, ed.edificio, cl.codigo, sec.dias
        FROM tbl_lista_cancelacion as cn
        INNER JOIN tbl_seccion as sec
        ON cn.seccion_id = sec.seccion_id
        INNER JOIN tbl_clase as cl
        ON sec.clase_id = cl.clase_id
        INNER JOIN tbl_aula as al
        ON sec.aula_id = al.aula_id
        INNER JOIN tbl_edificio as ed
        ON cl.edificio_id = ed.edificio_id
        WHERE estudiante_id = ?';

        return $this->fetchAll($sql, [$estudianteid]);
    }

    public function obtenerSolicitudCanceladas(){
        $sql = "SELECT tbl_usuario.nombre_completo,tbl_usuario.numero_cuenta,tbl_lista_cancelacion.motivo,tbl_clase.nombre,tbl_lista_cancelacion.documento, tbl_lista_cancelacion.estado,tbl_lista_cancelacion.matricula_id
        FROM tbl_lista_cancelacion          
        INNER JOIN tbl_matricula ON tbl_matricula.matricula_id = tbl_lista_cancelacion.matricula_id          
        INNER JOIN tbl_estudiante ON tbl_estudiante.estudiante_id = tbl_matricula.estudiante_id          
        INNER JOIN tbl_usuario ON tbl_usuario.usuario_id=tbl_estudiante.usuario_id INNER JOIN tbl_seccion ON tbl_matricula.seccion_id = tbl_seccion.seccion_id 
        INNER JOIN tbl_clase ON tbl_seccion.clase_id = tbl_clase.clase_id
        WHERE tbl_lista_cancelacion.estado = 'Pendiente'";

        return $this->fetchAll($sql);
    }

}
?>