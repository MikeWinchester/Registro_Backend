<?php
require_once __DIR__ . "/../models/Estudiante.php";
require_once __DIR__ . "/BaseController.php";

class EstudianteController extends BaseController{
    private $estudiante;
    

    public function __construct() {
        parent::__construct();
        $this->estudiante = new Estudiante();
    }


    public function getEspEstudiante(){
        $header = getallheaders();



        $estudiante = $header['estudianteid'];
        

        $result = $this->estudiante->obtenerEsperaEstudiante($estudiante);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }
    }


    public function getEstudiante(){
        $header = getallheaders();

        $estudiante = $header['estudianteid'];
        

        $result = $this->estudiante->obtenerPerfilEstudiante($estudiante);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }
    }

    public function getEstudianteByCuenta(){
        $header = getallheaders();

        $estudiante = $header['cuenta'];
        
        $result = $this->estudiante->obtenerEstudianteByCuenta($estudiante);

        $result[0]['indice_global'] = $this->getIndiceGlobal($estudiante);
        $result[0]['indice_periodo'] = $this->getIndicePeriodo($estudiante);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Estudiante encontrado", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Estudiante  no encontrado"]);
        }
    }

    private function getIndiceGlobal($numero_cuenta){
       

        $result = $this->estudiante->obtenerIndiceGlobal($numero_cuenta);

        if($result){
            return $result[0]['indice_global'];
        }

        return 0;
    }

    private function getIndicePeriodo($numero_cuenta){
        

        $result = $this->estudiante->obtenerIndicePeriodo($numero_cuenta, $this->getPeriodo());

        if($result[0]['indice_periodo'] != null){
            return $result[0]['indice_periodo'];
        }

        $result = $this->estudiante->obtenerIndicePeriodo($numero_cuenta, $this->getPeriodoPasado());

        if($result[0]['indice_periodo'] != null){
            return $result[0]['indice_periodo'];
        }

        return 0;
    }


    public function getHistorial(){
        $header = getallheaders();

       
        $estudiante = $header['cuenta'];
        
        $result = $this->estudiante->obtenerHistorialByCuenta($estudiante);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Historial encontrado", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Historial no encontrado"]);
        }
    }

    public function getUsuarioByEstu(){
        $header = getallheaders();


        $result = $this->estudiante->obtenerUsuarioByEstudiante($header['estudianteid']);

        if($result){
            http_response_code(200);
            echo json_encode(['message' => 'usuario obtenido', "data" => $result]);
        }else{
            http_response_code(400);
            echo json_encode(['error' => 'usuario no obtenido']);
        }
    }

    /**
     * Funcion para obtener el periodo acadmico actual
     *
     * @return "anio-trimestre" ejemplo: "2021-1"
     * 
     * @version 0.1.1
     */
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

    private function getPeriodoPasado() {
        
        $periodoActual = $this->getPeriodo();
        $anio = explode("-",$periodoActual)[0];
        $periodo = explode("-",$periodoActual)[1];

        if($periodo == 'I'){
            $anio = intval($anio) - 1;
            $periodo = 'III';
        }elseif($periodo == 'II'){
            $periodo = 'I';
        }elseif($periodo == 'III'){
            $periodo = 'II';
        }
    
        return "$anio-$periodo";
    }
}
?>