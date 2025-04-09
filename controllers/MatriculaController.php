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
            $resultSec = $this->matricula->actualizarSeccion([$secId]);
            $this->cancelacion->createCancelacion(["seccion_id" => $secId, "estudiante_id" => $estId]);
            if($resultSec){
                http_response_code(200);
                echo json_encode(["message" => "Seccion cancelada"]);
            }
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
        $header = getallheaders();
        $estudianteId = $header['estudianteid'] ?? null;
    
        $rango = $this->matricula->obtenerFechaMatricula();
    
        if (!$rango) {
            http_response_code(404);
            echo json_encode(["error" => "No hay rango de matrícula definido"]);
            return;
        }
    
        $inicio = new DateTime($rango[0]['inicio']);
        $final = new DateTime($rango[0]['final']);
        $hoy = new DateTime();
    
        if ($hoy < $inicio || $hoy > $final) {
            http_response_code(404);
            echo json_encode(["error" => "Fuera del horario de matrícula"]);
            return;
        }
    
        $diasTotales = $inicio->diff($final)->days + 1;
        $diasTranscurridos = $inicio->diff($hoy)->days;
    
        $promedios = [85, 80, 75, 70, 65, 60, 0];
        $promediosDistribuidos = array_slice($promedios, 0, $diasTotales);
        $promedioRequerido = $promediosDistribuidos[$diasTranscurridos];
    
        $indice = $this->getIndiceGlobal($estudianteId);
    
        if (is_null($indice)){
            http_response_code(200);
            echo json_encode(["message" => "Primer ingreso: dentro del horario de matrícula"]);
            return;
        }

        if ($indice >= $promedioRequerido) {
            http_response_code(200);
            echo json_encode(["message" => "Índice válido: dentro del horario de matrícula"]);
        } else {
            http_response_code(403);
            echo json_encode(["error" => "Se requiere mínimo de {$promedioRequerido} para este día."]);
        }
    }
    
    
    private function getIndiceGlobal($estudianteid){
       
    
        $res = $this->matricula->obtenerIndiceGlobal([$estudianteid]);
        return $res && isset($res['promedio']) ? floatval($res['promedio']) : null;
    }
    
    
    
}



?>
