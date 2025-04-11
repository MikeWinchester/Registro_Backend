<?php
require_once __DIR__ . "/../models/InfoNotas.php";
require_once __DIR__ . "/BaseController.php";

class InfoNotasControllerr extends BaseController{
    private $info;
    

    public function __construct() {
        parent::__construct();
        $this->info = new InfoNotas();
    }

    public function setFecha(){

        header('Content-Type: application/json');
    
        $data = json_decode(file_get_contents("php://input"), true);

        $this->info->desactivarNotas();

        $data['estado_matricula_id'] = 1;

        $return = $this->info->create($data);

        if($return){
            http_response_code(200);
            echo json_encode(['message' => 'Se han establecido las fechas']);
        }else{
            http_response_code(400);
            echo json_encode(['error' => 'No se han establecido las fechas']);
        }
        
    }

    public function getHorario() {
       
        $rango = $this->info->obtenerHorario();
    
        if (!$rango) {
            http_response_code(404);
            echo json_encode(["error" => "No hay horario disponible"]);
            return;
        }
    
        $inicio = new DateTime($rango[0]['inicio']);
        $final = new DateTime($rango[0]['final']);
        $diasTotales = $inicio->diff($final)->days + 1;
    
        $promediosBase = [85, 80, 75, 70, 65, 60];
    
        $promediosDistribuidos = array_slice($promediosBase, 0, $diasTotales - 1);
        $promediosDistribuidos[] = 70;
    
        $horario = [];
    
        for ($i = 0; $i < $diasTotales; $i++) {
            $fecha = clone $inicio;
            $fecha->modify("+$i day");
            $horario[] = [
                "fecha" => $fecha->format("Y-m-d"),
                "promedio" => $promediosDistribuidos[$i]
            ];
        }
    
        http_response_code(200);
        echo json_encode(["horario" => $horario]);
    }
    
    

}

?>
