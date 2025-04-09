<?php
require_once __DIR__ . "/BaseModel.php";

class Mensaje extends BaseModel {


    public function __construct() {
        parent::__construct('tbl_mensajes', 'mensaje_id');
    }
}
?>