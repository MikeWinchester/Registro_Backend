<?php
require_once __DIR__ . "/../core/Model.php";

class Admisiones extends Model {

    public function __construct() {
        parent::__construct("usuario"); // La tabla que estamos usando
    }

}
?>