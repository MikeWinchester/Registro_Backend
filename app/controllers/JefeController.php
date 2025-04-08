<?php
require_once __DIR__ . "/../models/Jefe.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class JefeController {
    private $jefe;
    

    public function __construct() {
        $this->jefe = new Jefe();
    }


    /**
     * Obtener el departamento por el jefe
     * 
     * @version 0.1.0
     */
    public function getDepByJefe(){
        $header = getallheaders();

        if(!isset($header['jefeid'])){
            http_response_code(400);
            echo json_encode(["error" => "Campo jefeid necesario"]);
        }

        $sql = "SELECT departamento_id as departamentoid
        FROM tbl_jefe as jf
        INNER JOIN tbl_docente as dc
        ON jf.docente_id = dc.docente_id
        WHERE jefe_id = ?";

        $jefeID = $header['jefeid'];

        $result = $this->jefe->customQuery($sql, [$jefeID]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }
    }


    public function getFacByJefe(){

        $header = getallheaders();

        if(!isset($header['jefeid'])){
            http_response_code(400);
            echo json_encode(["error" => "Campo jefeid necesario"]);
        }

        $sql = "SELECT ft.facultad_id as facultadid
        FROM tbl_jefe as jf
        INNER JOIN tbl_docente as dc
        ON jf.docente_id = dc.docente_id
        INNER JOIN tbl_departamento as dep
        ON dep.departamento_id = dc.departamento_id
        INNER JOIN tbl_facultad as ft
        ON dep.facultad_id = ft.facultad_id
        WHERE jefe_id = ?";

        $jefeID = $header['jefeid'];

        $result = $this->jefe->customQuery($sql, [$jefeID]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }

    }

    public function getCentroByJefe($jefe){
        $sql = "SELECT centro_regional_id as id
                FROM tbl_jefe as jf
                INNER JOIN tbl_docente as dc
                ON jf.docente_id = dc.docente_id
                WHERE jefe_id = ?";

        return $this->jefe->customQuery($sql, [$jefe]);
    }
}
?>