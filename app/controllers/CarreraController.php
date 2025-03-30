<?php
require_once __DIR__ . "/../models/Carrera.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";

class CarreraController{

    private $career;

    public function __construct() {
        $this->career = new Carrera();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    public function getAllCareers() {
        echo json_encode(["message" => "Lista de carreras", "data" => $this->career->getAll()]);
    }

    public function getCarrera($estID){

        $sql = "SELECT carrera_id
        FROM tbl_estudiante
        WHERE estudiante_id = ?";

        $result = $this->career->customQuery($sql, [$estID]);

        return intval($result);

    }
}


?>