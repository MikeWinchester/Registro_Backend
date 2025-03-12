<?php
require_once __DIR__ . "/../core/Model.php";

class Usuario extends Model {
    public function __construct() {
        parent::__construct("usuario");
    }
}
?>
