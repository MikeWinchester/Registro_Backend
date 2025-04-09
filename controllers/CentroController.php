<?php
require_once __DIR__ . "/../models/Centro.php";
require_once __DIR__ . "/BaseController.php";

class CentroController extends BaseController{

    private $center;

    public function __construct() {
        parent::__construct();
        $this->center = new Centro();
        header("Content-Type: application/json"); 
    }

    public function getAllCenters() {
        echo json_encode(["message" => "Lista de centros regionales", "data" => $this->center->getAll()]);
    }

}
?>