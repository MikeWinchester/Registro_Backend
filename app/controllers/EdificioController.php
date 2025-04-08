<?php

require_once __DIR__ . "/../models/Edificio.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";


class EdificioController {
    private $edificio;

    public function __construct() {
        $this->edificio = new Edificio();
        
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    /**
     * retorna los edifcio de un centro
     * 
     * @version 0.1.0
     */
    public function getEdificioByJefe(){

        $header = getallheaders();

        if(!isset($header['jefeid'])){
            http_response_code(400);
            echo json_encode(['Error'=>'campo jefeid necesario']);
        }

        $sql = 'SELECT edificio_id,edificio
                FROM tbl_edificio as ed
                INNER JOIN tbl_docente as dc
                ON ed.centro_regional_id = dc.centro_regional_id
                INNER JOIN tbl_jefe as jf
                ON dc.docente_id = jf.docente_id
                WHERE jf.jefe_id = ?';

        $jefeid = $header['jefeid'];

        $result = $this->edificio->customQuery($sql, [$jefeid]);

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