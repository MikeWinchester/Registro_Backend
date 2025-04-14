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

    public function obtenerNotas($request){
        $est = $request->getRouteParam(0);

        error_log($est);


        $result = $this->notas->obtenerNotasEstu([$est, $this->getPeriodo()]);

        if($result){
            http_response_code(200);
            echo json_encode(['message' => 'Notas obtenidas con exito', 'data' => $result]);
        }else{
            http_response_code(400);
            echo json_encode(['message' => 'Notas no obtenidas con exito']);
        }
    }

    public function createEvaluacion(){
        $data = json_decode(file_get_contents("php://input"), true);

        $result = $this->notas->crearEvaluacion([$data['estudianteid'], $data['seccionid'], $data['calificacion'], $data['comentario']]);

        if($result){
            http_response_code(200);
            echo json_encode(['message'=>'Se ha evaluado al docente']);
        }else{
            http_response_code(200);
            echo json_encode(['message'=>'Docente ya evaluado']);
        }
    }

    private function getPeriodo() {
        $year = date("Y");
        $mon = date("n");
    
        if ($mon >= 1 && $mon <= 4) {
            $trimestre = "I";
        } elseif ($mon >= 5 && $mon <= 8) {
            $trimestre = "II";
        } else {
            $trimestre = "III";
        }
    
        return "$year-$trimestre";
    }

}


?>
