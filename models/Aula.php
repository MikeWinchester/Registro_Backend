<?php
require_once __DIR__ . "/BaseModel.php";

class Aula extends BaseModel {
    
    public function __construct() {
        parent::__construct('tbl_aula', 'aula_id');
    }

    public function existeaula($edi){
        $sql = 'SELECT count(1) as existe
        FROM tbl_aula as al
        INNER JOIN tbl_edificio as ed
        ON al.edificio_id = ed.edificio_id
        WHERE ed.edificio_id = ?';

        return $this->fetchAll($sql, [$edi]);
    }

    public function obtenerAulaEdificio($edi){
        $sql = 'SELECT aula_id, al.aula, ed.edificio
        FROM tbl_aula as al
        INNER JOIN tbl_edificio as ed
        ON al.edificio_id = ed.edificio_id
        WHERE ed.edificio_id = ?';

        return $this->fetchAll($sql, [$edi]);
    }
}

?>