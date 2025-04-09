<?php
require_once __DIR__ . "/../models/InfoMatricula.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class InfoMatriculaController {
    private $info;
    

    public function __construct() {
        $this->info = new InfoMatricula();
    }

    public function setFecha(){

        $data = json_decode(file_get_contents("php://input"), true);

        $sql = "UPDATE tbl_info_matricula SET estado_matricula_id = 0";

        $this->info->customQueryUpdate($sql);

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
        $sql = "SELECT * FROM tbl_info_matricula WHERE estado_matricula_id = 1 LIMIT 1";
        $rango = $this->info->customQuery($sql);
    
        if (!$rango) {
            http_response_code(404);
            echo json_encode(["error" => "No hay horario de matrícula disponible"]);
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
