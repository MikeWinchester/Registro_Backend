<?php
require_once __DIR__ . "/BaseModel.php";

class InfoMatricula extends BaseModel {


    public function __construct() {
        parent::__construct("tbl_info_matricula", 'inicio');
    }

    public function desactivarMatricula(){
        $sql = "UPDATE tbl_info_matricula SET estado_matricula_id = 0";

        $this->executeWrite($sql);
    }

    public function obtenerHorario(){
        $sql = "SELECT * FROM tbl_info_matricula WHERE estado_matricula_id = 1 LIMIT 1";

        return $this->fetchOne($sql);
    }
}

?>