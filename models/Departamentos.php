<?php
require_once __DIR__ . '/BaseModel.php';

class Departamentos extends BaseModel {

    public function __construct() {
        parent::__construct('tbl_departamento', 'departamento_id');
    }

}

?>