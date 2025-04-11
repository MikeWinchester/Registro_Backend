<?php
require_once __DIR__ . "/BaseModel.php";

class InfoNotas extends BaseModel {


    public function __construct() {
        parent::__construct("tbl_info_notas", 'inicio');
    }

    public function desactivarNotas(){
        $sql = "UPDATE tbl_info_notas SET estado_matricula_id = 2";

        $this->executeWrite($sql);
    }

    public function obtenerHorario(){
        $sql = "SELECT * FROM tbl_info_notas WHERE estado_matricula_id = 1 LIMIT 1";

        return $this->fetchOne($sql);
    }

}

?>