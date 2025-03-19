<?php
require_once __DIR__ . "/../models/Centro.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";

class CentroController{

    private $center;

    public function __construct() {
        $this->center = new Centro();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    public function getAllCenters() {
        echo json_encode(["message" => "Lista de centros regionales", "data" => $this->center->getAll()]);
    }

}
?>