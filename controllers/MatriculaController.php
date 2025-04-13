<?php

require_once __DIR__ . "/../models/Matricula.php";
require_once __DIR__ . "/../controllers/CancelacionController.php";
require_once __DIR__ . "/../models/Espera.php";
require_once __DIR__ . "/BaseController.php";

class MatriculaController extends BaseController{

    private $matricula;
    private $espera;
    private $cancelacion;

    public function __construct()
    {
        parent::__construct();
        $this->espera = new Espera();
        $this->matricula = new Matricula();
        $this->cancelacion = new CancelacionController();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

 
    public function getEstudiantes(){


        $header = getallheaders();
    
        $secID = $header['seccionid'];

        $result = $this->matricula->obtenerEstudianteBySeccion($secID);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Estudiantes encontrados de seccion", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Estudiantes no disponibles en la seccion"]);
        }


    }

    public function getEstudiantesNotas(){


        $header = getallheaders();
    
        $secID = $header['seccionid'];



        $result = $this->matricula->obtenerEstudiantesByNotas([$secID]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Estudiantes encontrados", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Estudiantes no esta disponibles"]);
        }


    }


    public function setMatricula() {
        $data = json_decode(file_get_contents("php://input"), true);

        $result = $this->matricula->revisarMatricula($data);
    
        
        if (intval($result['existe']) == 0) {

            $result = $this->cumpleHorario($data);
            
            if(intval($result['existe'] == 0)){

                if ($this->revisionCupos($data)) {
                    unset($data['clase_id']);
                    $result = $this->matricula->create($data);

                    if (!$result) {
                        http_response_code(200);
                        echo json_encode(["message" => "Se ha matriculado la clase", "data" => 'Clase Matriculada']);
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Error al matricula la clase"]);
                    }

                } else {
                    
                    $this->matricularEspera($data);
                }
            }else{
                http_response_code(200);
                echo json_encode(["message" => "Conflicto de horario"]);
            }

        } else {
            http_response_code(200);
            echo json_encode(["message" => "El estudiante ya está en espera", "data" => 'Clase Matriculada']);
        }
    }

    private function cumpleHorario($data){
        
        return $this->matricula->cumpleHorario([$data['seccion_id'], $data['estudiante_id'], $this->getPeriodo()]);
    }

    private function revisionCupos($data){
        $cupo_ocupados = $this->matricula->cupos_ocupados([$data['seccion_id']]);
        $cupo_seccion = $this->matricula->cupo_seccion([$data['seccion_id']]);

        $cupos_disponibles = (intval($cupo_seccion['cupo_maximo']) - intval($cupo_ocupados['estudiantes']));
        
        if($cupos_disponibles > 0){
            return true;
        }
        return false;
    }

    private function matricularEspera($data){
        $esperaData = ['seccion_id' => $data['seccion_id'], 'estudiante_id' => $data['estudiante_id']];
        $result = $this->espera->create($esperaData);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Se ha agregado a lista de espera"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "No se logró agregar en espera"]);
        }
    }
    
    public function cumpleRequisito(){
        $header = getallheaders();

        $est = $header['estudianteid'];
        $cla = $header['claseid'];

        $result = $this->matricula->comprobarRequisitos([$est, $cla, $cla]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Cumple los requisitos", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No cumple los requisitos"]);
        }

        
    }


    public function getMatriculaEst(){

        $header = getallheaders();
    
        $estId = $header['estudianteid'];

        $result = $this->matricula->obtenerEstudiantesMatriculados([$estId, $this->getPeriodo()]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Estudiantes encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Estudiantes no disponibles"]);
        }
    }

    public function getHistMat(){
        $header = getallheaders();
    
        $estId = $header['estudianteid'];

        $result = $this->matricula->obtenerHistorialMatricula([$estId]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Estudiantes encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Estudiantes no disponibles"]);
        }
    }

    public function delMat(){

        $header = getallheaders();
    
        $estId = $header['estudianteid'];
        $secId = $header['seccionid'];

        $resultMAT = $this->matricula->eliminarMatricula([$estId, $secId]);

        if ($resultMAT) {
            $this->cancelacion->createCancelacion(["seccion_id" => $secId, "estudiante_id" => $estId]);
            
            http_response_code(200);
            echo json_encode(["message" => "Seccion cancelada"]);
            
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se completo la cancelacion"]);
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

    public function permitirMatriculaEstu() {
        date_default_timezone_set('America/Tegucigalpa');
    
        $header = getallheaders();

        $estudianteId = $header['id'];

        $rango = $this->matricula->obtenerFechaMatricula();
    
        $inicio = new DateTime($rango['inicio']);
        $final = new DateTime($rango['final']);
        $hoy = new DateTime();
    
        if ($hoy < $inicio || $hoy > $final) {
            $this->validateAddCan($hoy);
        }else{
            $this->validateMatricula($inicio, $final, $hoy, $estudianteId);
        }
    
        
    }

    private function validateMatricula($inicio, $final, $hoy, $estudianteId){
        $diasTotales = $inicio->diff($final)->days + 1;
            $diasTranscurridos = $inicio->diff($hoy)->days;
        
            $promedios = [85, 83, 75, 70, 65];
            $promediosDistribuidos = array_slice($promedios, 0, $diasTotales);
            $promedioRequerido = $promediosDistribuidos[$diasTranscurridos];
            if($diasTranscurridos > 0){
                $promedioSuperior = $promediosDistribuidos[$diasTranscurridos-1];
            }else{
                $promedioSuperior = 100;
            }
        
            $indice = $this->getIndiceGlobal($estudianteId);
        
            if (is_null($indice)){
                http_response_code(200);
                echo json_encode(["message" => "Primer ingreso: dentro del horario de matrícula", 'validate' => true]);
                return;
            }

            if($hoy == $final){
                if ($indice <= $promedioRequerido) {
                    http_response_code(200);
                    echo json_encode(["message" => "Índice válido: dentro del horario de matrícula", 'validate' => true]);
                } 
            }else{
                if ($indice >= $promedioRequerido && $indice <= $promedioSuperior) {
                    http_response_code(200);
                    echo json_encode(["message" => "Índice válido: dentro del horario de matrícula", 'validate' => true]);
                } else {
                    http_response_code(403);
                    echo json_encode(["error" => "Se requiere mínimo de {$promedioRequerido} para este día.", "validate" => false]);
                }
            }
    }

    private function validateAddCan($hoy){
        $rango = $this->matricula->obtenerFechaAddCan();
    
        $inicio_add = new DateTime($rango['inicio']);
        $final_add = new DateTime($rango['final']);
        if($hoy < $inicio_add || $hoy > $final_add){
            http_response_code(404);
            echo json_encode(["error" => "Fuera del horario de adiccion y cancelacion", 'validate' => false, 'inicio fin' => [$inicio_add, $final_add] ]);
        }else{
            http_response_code(200);
            echo json_encode(["message" => "Dentro del horario de adiccion y cancelacion", 'validate' => true]);
        }
    }
    
    
    private function getIndiceGlobal($estudianteid){
       
    
        $res = $this->matricula->obtenerIndiceGlobal([$estudianteid]);
        return $res && isset($res['promedio']) ? floatval($res['promedio']) : null;
    }
    
    
    
}



?>
