<?php
require_once __DIR__ . "/../core/Model.php";

class Cancelacion extends Model {

    public function __construct() {
        parent::__construct("tbl_lista_cancelacion");
    }

}
?>