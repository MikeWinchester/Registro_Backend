<?php

require_once __DIR__ . "/../models/Notas.php";
require_once __DIR__ . "/BaseController.php";

class NotasController extends BaseController{

    private $notas;

    public function __construct()
    {
        parent::__construct();
        $this->notas = new Notas();
    }


    public function asigNotas() {
        $data = json_decode(file_get_contents("php://input"), true);

        foreach ($data as $key => $estudiante) {
            if (!isset($estudiante["estudiante_id"]) || !isset($estudiante["seccion_id"]) || !isset($estudiante["nota"]) || !isset($estudiante["observacion_id"])) {
                http_response_code(400);
                echo json_encode(["error" => "Faltan datos requeridos en $key"]);
                return;
            }
    
            
            $notas = [
                "estudiante_id" => $estudiante["estudiante_id"],
                "seccion_id"    => $estudiante["seccion_id"],
                "calificacion" => $estudiante["nota"],
                "observacion_id" => $estudiante['observacion_id']
            ];

            $result = $this->notas->create($notas);

            if ($result) {
                http_response_code(200);
                echo json_encode(["message" => "Notas asignadas correctamente", "data" => $result]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "No se pudieron asignar las notas"]);
            }
        }
    
        
    }

    public function searchNotas(){
        $header = getallheaders();
    
       
        $result = $this->notas->buscarNotas($header);
    
        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Notas encontradas correctamente", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se encontraron notas"]);
        }
    }
    
    public function permitirNotas() {
        date_default_timezone_set('America/Tegucigalpa');
    
        $rango = $this->notas->obtenerFechaNotas();
    
        $inicio = new DateTime($rango['inicio']);
        $final = new DateTime($rango['final']);
        $hoy = new DateTime();
    
        if ($hoy <= $inicio || $hoy >= $final) {
            http_response_code(404);
            echo json_encode(["error" => "Fuera del horario de registro de notas", 'validate' => false]);
            return;
        }else{
            http_response_code(200);
            echo json_encode(["message" => "Fecha valida: dentro del horario de registro", 'validate' => true]);
        }
            
        
    }

}


?>
