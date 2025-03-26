<?php
require_once __DIR__ . "/../core/Model.php";

class Jefe extends Model {
    public function __construct() {
        parent::__construct("tbl_jefe");
    }
}
?>