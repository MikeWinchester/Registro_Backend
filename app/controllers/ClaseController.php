<?php
require_once __DIR__ . "/../models/Clase.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class ClaseController {
    private $clase;
    

    public function __construct() {
        $this->clase = new Clase();
    }


    /**
     * Obtiene todos las clases por departamento
     *
     * @version 0.1.1
     */
    public function getClasesByDepartment(){

        #AuthMiddleware::authMiddleware();

        $header = array_change_key_case(getallheaders(), CASE_LOWER);
        if (!isset($header['departamentoid'])) {
            http_response_code(400);
            echo json_encode(["error" => "DepartamentoID es requerido en el header"]);
            return;
        }

        $depID = $header['departamentoid']; // Accede en minúsculas


        $sql = "SELECT cl.clase_id, cl.nombre, cl.codigo, cl.UV
        FROM tbl_clase AS cl
        WHERE cl.departamento_id = ?";

        $result = $this->clase->customQuery($sql, [$depID]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "clases obtenidas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Clases no obtenidas"]);
        }
    }

    /**
     * Crea una clase
     *
     * @version 0.1.0
     */
    public function createClases(){

        #AuthMiddleware::authMiddleware();

        $data = json_decode(file_get_contents("php://input"), true);
        
        if ($this->clase->create($data)) {
            http_response_code(200);
            echo json_encode(["message" => "clase creada", "data" => $data]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Clase no creada"]);
        }
    }

    /**
     * retorna el id de un edificio por una clase
     * 
     * @version 0.1.0
     */
    public function getEdidByClass(){

        $header = array_change_key_case(getallheaders(), CASE_LOWER);
        if (!isset($header['claseid'])) {
            http_response_code(400);
            echo json_encode(["error" => "Claseid es requerido en el header"]);
            return;
        }

        $claseID = $header['claseid']; // Accede en minúsculas


        $sql = "SELECT cl.edificio_id
        FROM tbl_clase AS cl
        WHERE cl.clase_id = ?";

        $result = $this->clase->customQuery($sql, [$claseID]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "clases obtenidas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Clases no obtenidas"]);
        }

    }
    
}

?>
