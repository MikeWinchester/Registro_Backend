<?php
require_once __DIR__ . "/BaseModel.php";

class Observacion extends BaseModel {

    public function __construct() {
        parent::__construct("tbl_observacion", "observacion_id");
    }
}
?>