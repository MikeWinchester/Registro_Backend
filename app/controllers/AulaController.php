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

    /**
     * retorna las aulas de un edificio
     * 
     * @version 0.1.0
     */
    public function getAulasByEdificio(){

        $header = getallheaders();

        if(!isset($header['edificioid'])){
            http_response_code(400);
            echo json_encode(['Error'=>'campo EdificioID necesario']);
        }

        $sql = 'SELECT aula_id, aula
                FROM tbl_aula
                WHERE edificio_id = ?';

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
