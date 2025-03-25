<?php

require_once __DIR__ . "/../models/Aula.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class AulaController {
    private $aula;

    public function __construct() {
        $this->aula = new Aula();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    public function getAulasByEdificio(){

        $header = getallheaders();

        if(!isset($header['edificioid'])){
            http_response_code(400);
            echo json_encode(['Error'=>'campo EdificioID necesario']);
        }

        $sql = 'SELECT AulaID, Aula
                FROM Aula
                WHERE EdificioID = ?';

        $edificioID = $header['edificioid'];

        $result = $this->aula->customQuery($sql, [$edificioID]);

        if ($result) {
            echo json_encode(["message" => "Aula obtenida correctamente", 'data' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener Aula"]);
        }
    }

}
?>
