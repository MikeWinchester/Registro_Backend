<?php
require_once __DIR__ . "/../core/Model.php";

class Espera extends Model {
    public function __construct() {
        parent::__construct("tbl_lista_espera");
    }
}
?>