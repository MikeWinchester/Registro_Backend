<?php
require_once __DIR__ . "/../models/Clase.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class ClaseController {
    private $clase;
    

    public function __construct() {
        $this->clase = new Clase();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }


    /**
     * Obtiene todos las clases por departamento
     *
     * @version 0.1.1
     */
    public function getClasesByDepartment(){

        #AuthMiddleware::authMiddleware();

        $header = getallheaders();

        if(!isset($header['DepartamentoID'])){
            http_response_code(400);
            echo json_encode(["error" => "CentroRegionalID es requerido en el header"]);
            return;
        }
    
        $depID = $header['DepartamentoID'];

        $sql = "SELECT cl.ClaseID, cl.Nombre, cl.Codigo
        FROM Clase AS cl
        WHERE cl.DepartamentoID = ?";

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

    
}

?>
