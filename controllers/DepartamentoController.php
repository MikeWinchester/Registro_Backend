<?php

require_once __DIR__ . "/../models/Departamentos.php";
require_once __DIR__ . "/BaseController.php";

class DepartamentoController extends BaseController{
    private $dep;

    public function __construct() {
        parent::__construct();
        $this->dep = new Departamentos();
        header("Content-Type: application/json"); 
    }

    public function detDeps(){
        $result = $this->dep->getAll();

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Departamento obtenida correctamente", 'data' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener Departamentos"]);
        }
    
    }


}
?>
