<?php
require_once __DIR__ . "/BaseModel.php";

class Usuario extends BaseModel {
    public function __construct() {
        parent::__construct("tbl_usuario", "usuario_id");
    }
}
?>
