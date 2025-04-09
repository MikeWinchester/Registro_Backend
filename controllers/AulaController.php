<?php

require_once __DIR__ ."/BaseController.php";
require_once __DIR__ . "/../models/Aula.php";
require_once __DIR__ . "/../controllers/JefeController.php";

class AulaController extends BaseController{
    private $aula;
    private $jefe;

    public function __construct() {
        parent::__construct();
        $this->aula = new Aula();
        $this->jefe = new JefeController();
        header("Content-Type: application/json");
    }

    public function getAulasByEdificio(){

        $header = getallheaders();

        $edificioid = $header['edificioid'];

        $resultExist = $this->aula->existeaula($edificioid);

        if($resultExist[0]['existe'] > 0){

           

            $result = $this->aula->obtenerAulaEdificio($edificioid);

            if ($result) {
                http_response_code(200);
                echo json_encode(["message" => "Aula obtenida correctamente", 'data' => $result]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al obtener Aula"]);
            }
        }else{
            http_response_code(200);
            echo json_encode(["message" => "no hay aulas disponibles", 'data' => null]);
        }
    }

}
?>
