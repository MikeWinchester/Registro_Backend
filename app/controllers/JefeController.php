<?php
require_once __DIR__ . "/../models/Jefe.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class JefeController {
    private $jefe;
    

    public function __construct() {
        $this->jefe = new Jefe();
    }


    public function getDepByJefe(){
        $header = getallheaders();

        if(!isset($header['jefeid'])){
            http_response_code(400);
            echo json_encode(["error" => "Campo jefeid necesario"]);
        }

        $sql = "SELECT carrera_id as carreraid
        FROM tbl_jefe
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
}
?>