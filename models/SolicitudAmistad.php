<?php
require_once __DIR__ . "/BaseModel.php";

class SolicitudAmistad extends BaseModel {


    public function __construct() {
        parent::__construct("tbl_solicitud", 'usuario_emisor');
    }
}

?>