<?php

require_once __DIR__ . "/../models/Edificio.php";
require_once __DIR__ . "/BaseController.php";


class EdificioController extends BaseController{
    private $edificio;

    public function __construct() {
        parent::__construct();
        $this->edificio = new Edificio();
        
        header("Content-Type: application/json");
    }


    public function getEdificioByJefe(){

        $header = getallheaders();

        if(!isset($header['jefeid'])){
            http_response_code(400);
            echo json_encode(['Error'=>'campo jefeid necesario']);
        }

        $jefeid = $header['jefeid'];

        $result = $this->edificio->obtenerEdificioByJefe($jefeid);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Edificio obtenidos correctamente", 'data' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener Edificio"]);
        }
    
    }

}
?>