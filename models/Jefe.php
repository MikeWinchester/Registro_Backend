<?php
require_once __DIR__ . "/BaseModel.php";

class Jefe extends BaseModel {


    public function __construct() {
        parent::__construct("tbl_jefe", "jefe_id");
    }

    public function getDepartamentoByJefe($jefeid){
        $sql = "SELECT departamento_id as departamentoid
        FROM tbl_jefe as jf
        INNER JOIN tbl_docente as dc
        ON jf.docente_id = dc.docente_id
        WHERE jefe_id = ?";

        return $this->fetchOne($sql, [$jefeid]);
    }

    public function obtenerFacultadByJefe($jefeid){
        $sql = "SELECT ft.facultad_id as facultadid
        FROM tbl_jefe as jf
        INNER JOIN tbl_docente as dc
        ON jf.docente_id = dc.docente_id
        INNER JOIN tbl_departamento as dep
        ON dep.departamento_id = dc.departamento_id
        INNER JOIN tbl_facultad as ft
        ON dep.facultad_id = ft.facultad_id
        WHERE jefe_id = ?";

        return $this->fetchOne($sql, [$jefeid]);
    }

    public function obtenerCentroByJefe($jefeid){
        $sql = "SELECT centro_regional_id as id
                FROM tbl_jefe as jf
                INNER JOIN tbl_docente as dc
                ON jf.docente_id = dc.docente_id
                WHERE jefe_id = ?";

        return $this->fetchOne($sql, [$jefeid]);
    }

    public function getUsuarioByJefe($jefeid){
        $sql = "SELECT usuario_id 
                FROM tbl_jefe AS jf
                INNER JOIN tbl_docente AS dc
                ON jf.docente_id = dc.docente_id
                WHERE jefe_id = ?";

        return $this->fetchOne($sql, [$jefeid]);
    }
}

?>
