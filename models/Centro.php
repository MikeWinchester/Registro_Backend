<?php
require_once __DIR__ . "/BaseModel.php";

class Centro extends BaseModel {
    public function __construct() {
        parent::__construct("tbl_centro_regional", "centro_regional_id");
    }
}
?>