<?php
require_once __DIR__ . "/../core/Model.php";

class Centro extends Model {
    public function __construct() {
        parent::__construct("centroregional");
    }
}
?>