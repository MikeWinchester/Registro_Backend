<?php
require_once __DIR__ . "/../models/Carrera.php";
require_once __DIR__ . "/BaseController.php";

class CarreraController extends BaseController{

    private $career;

    public function __construct() {
        parent::__construct();
        $this->career = new Carrera();
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