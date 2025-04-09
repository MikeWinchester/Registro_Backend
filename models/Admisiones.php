<?php
require_once __DIR__ . "/BaseModel.php";

class Admisiones extends BaseModel {

    public function __construct() {
        parent::__construct("tbl_admision", 'admision_id');
    }

}
?>
