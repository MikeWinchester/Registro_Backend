<?php
require_once __DIR__ . "/BaseModel.php";

class Edificio extends BaseModel {


    public function __construct() {
        parent::__construct("tbl_edificio", "edificio_id");
    }

    public function obtenerEdificioByJefe($jefeid){
        $sql = 'SELECT edificio_id,edificio
                FROM tbl_edificio as ed
                INNER JOIN tbl_docente as dc
                ON ed.centro_regional_id = dc.centro_regional_id
                INNER JOIN tbl_jefe as jf
                ON dc.docente_id = jf.docente_id
                WHERE jf.jefe_id = ?';

        return $this->fetchAll($sql, [$jefeid]);
    }
}

?>