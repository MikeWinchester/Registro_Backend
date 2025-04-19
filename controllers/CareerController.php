<?php
require_once __DIR__ . "/../models/Career.php";
require_once __DIR__ . "/BaseController.php";

class CareerController extends BaseController{

    private $career;

    public function __construct() {
        parent::__construct();
        $this->career = new Career();
        header("Content-Type: application/json"); 
    }

    public function getAllCareers() {
        echo json_encode(["message" => "Lista de carreras", "data" => $this->career->getAll()]);
    }

    public function getCarrera($estID){

        return $this->career->obtenerCarreraEstu($estID);

    }
}


?>