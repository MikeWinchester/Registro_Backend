<?php

require_once __DIR__ . "/../models/Notas.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class NotasController{

    private $notas;

    public function __construct()
    {
        $this->notas = new Notas();
    }

    public function asigNotas(){
        $data = json_decode(file_get_contents("php://input"), true);

        if(!isset($data["EstudianteID"]) || !isset($data["SeccionID"]) || !isset($data["Calificacion"])){
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos requeridos"]);
            return;
        }

        $result = $this->notas->create($data);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Nota Asignada", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Notas no asignadas"]);
        }
    }


}


?>
