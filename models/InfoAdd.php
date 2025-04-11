<?php
require_once __DIR__ . "/BaseModel.php";

class InfoAdd extends BaseModel {


    public function __construct() {
        parent::__construct("tbl_info_add_can", 'inicio');
    }

    public function desactivarAdd(){
        $sql = "UPDATE tbl_info_add_can SET estado_matricula_id = 2";

        $this->executeWrite($sql);
    }

    public function obtenerHorario(){
        $sql = "SELECT * FROM tbl_info_add_can WHERE estado_matricula_id = 1 LIMIT 1";

        return $this->fetchOne($sql);
    }

}

?>