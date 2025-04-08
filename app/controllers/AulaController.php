<?php

require_once __DIR__ . "/../models/Aula.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

require_once __DIR__ . "/../controllers/JefeController.php";

class AulaController {
    private $aula;
    private $jefe;

    public function __construct() {
        $this->aula = new Aula();
        $this->jefe = new JefeController();
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
            echo json_encode(['Error'=>'campo centroid necesario']);
        }

        $sql = 'SELECT count(1) as existe
                FROM tbl_aula as al
                INNER JOIN tbl_edificio as ed
                ON al.edificio_id = ed.edificio_id
                WHERE ed.edificio_id = ?';

        $edificioid = $header['edificioid'];

        $resultExist = $this->aula->customQuery($sql, [$edificioid]);

        if($resultExist[0]['existe'] > 0){

            $sql = 'SELECT aula_id, al.aula, ed.edificio
                FROM tbl_aula as al
                INNER JOIN tbl_edificio as ed
                ON al.edificio_id = ed.edificio_id
                WHERE ed.edificio_id = ?';

            $result = $this->aula->customQuery($sql, [$edificioid]);

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
