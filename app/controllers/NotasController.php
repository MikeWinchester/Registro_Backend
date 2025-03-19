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

    /**
     * Asigna notas al estudiantes
     *
     * @version 0.1.0
     */
    public function asigNotas() {
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "Datos JSON inválidos"]);
            return;
        }
        
        foreach ($data as $key => $estudiante) {
            if (!isset($estudiante["EstudianteId"]) || !isset($estudiante["SeccionId"]) || !isset($estudiante["Nota"])) {
                http_response_code(400);
                echo json_encode(["error" => "Faltan datos requeridos en $key"]);
                return;
            }
    
            
            $notas = [
                "EstudianteID" => $estudiante["EstudianteId"],
                "SeccionID"    => $estudiante["SeccionId"],
                "Calificacion" => $estudiante["Nota"]
            ];

            $result = $this->notas->create($notas);
        }
    
        
    
        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Notas asignadas correctamente", "data" => $result]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "No se pudieron asignar las notas"]);
        }
    }
    


}


?>
