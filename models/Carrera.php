<?php
require_once __DIR__ . "/BaseModel.php";

class Carrera extends BaseModel {
    public function __construct() {
        parent::__construct("tbl_carrera", 'carrera_id');
    }

    public function obtenerCarreraEstu($estudianteid){

        $sql = "SELECT carrera_id as carrera
        FROM tbl_estudiante
        WHERE estudiante_id = ?";

        return $this->fetchOne($sql, [$estudianteid]);
    }

    
}
?>
