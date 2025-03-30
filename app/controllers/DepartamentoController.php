<?php

require_once __DIR__ . "/../models/Departamentos.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class DepartamentoController {
    private $dep;

    public function __construct() {
        $this->dep = new Departamentos();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    /**
     * retorna las areas 
     * 
     * @version 0.1.0
     */
    public function detDeps(){

        $result = $this->dep->getAll();

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Aula obtenida correctamente", 'data' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener Aula"]);
        }
    
    }


}
?>
