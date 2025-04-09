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


}
?>