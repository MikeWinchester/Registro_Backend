<?php

require_once __DIR__ . "/../models/Seccion.php";
require_once __DIR__ . "/../models/Matricula.php";
require_once __DIR__ . "/BaseController.php";

class SeccionesController extends BaseController{
    private $seccion;
    private $mat;

    public function __construct() {
        parent::__construct();
        $this->seccion = new Seccion();
        $this->mat = new Matricula();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    public function getSeccionesActuales(){

        $header = getallheaders();
        
        $docenteid = $header['docenteid'];

        $result = $this->seccion->obtenerSeccionesActuByDoc([$docenteid, $this->getPeriodo()]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones Actuales encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones Actuales no disponibles"]);
        }

    }


    public function getSecciones(){

        $header = getallheaders();
        
        $docenteid = $header['docenteid'];
        
        $result = $this->seccion->obtenerSeccionesByDoc([$docenteid]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Todas las Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no estan disponibles"]);
        }

    }

    public function getSeccionesOutParams() {
    
        // Ejecutamos la consulta
        $result = $this->seccion->obtenerSeccionesSinParametros();
    
        // Verificamos si la consulta devuelve resultados
        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }
    }


    public function getSeccion(){

        $header = getallheaders();
        
        $secID = $header['seccionid'];
        
        $result = $this->seccion->obtenerSeccion([$secID]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Seccion encontrada", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Seccion no disponible"]);
        }
    }
    

    public function getSeccionCount(){


        $header = getallheaders();

        
        $docenteid = $header['docenteid'];


        $result = $this->seccion->obtenerCantidadSeccion([$docenteid, $this->getPeriodo()]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Cantidad de seccion encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No hay Secciones  disponibles"]);
        }
    }


    public function createSeccion() {
        header('Content-Type: application/json');
    
        $data = json_decode(file_get_contents("php://input"), true);

        $centroID = $this->seccion->obtenerCentroByJefe($data['jefeID']);

        unset($data['jefeID']);
        $data['periodo_academico'] = $this->getPeriodo();

        $data['centro_regional_id'] = $centroID['id'];
    
        if ($this->validateSec($data) == 0) {
            if ($this->seccion->create($data)) {
                echo json_encode(["message" => "Seccion creada", "data" => $data]);
                http_response_code(200);
                exit(); 
            } else {
                http_response_code(400);
                echo json_encode(["error" => "No se logró crear la sección"]);
                exit();
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Docente ocupado en el mismo horario"]);
            exit();
        }
    }
    

    private function validateSec($data){

        $result = $this->seccion->validarSeccion([$data['docente_id'], $data['horario'], $this->getPeriodo(), $data['dias']]);

        return $result['existe'];
    }

    public function getSeccionesByClassEstu(){

        $header = getallheaders();

        $claseID = $header['claseid'];
        $estu = $header['estudianteid'];
        $centro = $this->seccion->getCentroByEstu([$estu]);

        $result = $this->seccion->obtenerSeccionesByEstu([$claseID, $this->getPeriodo(), $centro['id']]);
        
        $index = 0;
        foreach ($result as $seccion) {
            $result[$index]['cupo_maximo'] = $this->getCupos($seccion['seccion_id']);
            $index += 1;
        }

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones del Estudiante encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no estan disponibles"]);
        }
    }

    public function getSeccionesByClass(){

        $header = getallheaders();


        $claseID = $header['claseid'];
        $jefe = $header['jefeid'];
        $centro = $this->seccion->getCentroByJefe($jefe)['id'];

        $result = $this->seccion->obtenerSeccionesByClassCentro([$claseID, $this->getPeriodo(), $centro]);
        
        $index = 0;
        foreach ($result as $seccion) {
            $result[$index]['cupo_maximo'] = $this->getCupos($seccion['seccion_id']);
            $index += 1;
        }

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones Por Clase encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones se encuentan no disponibles"]);
        }
    }

    public function getSeccionesByClassDoc(){

        $header = getallheaders();

        $claseID = $header['claseid'];
        $docID = $header['docenteid'];

        $result = $this->seccion->obtenerSeccionClaseByDoc([$claseID, $docID,$this->getPeriodo()]);
        
        $index = 0;
        foreach ($result as $seccion) {
            $result[$index]['cupo_maximo'] = $this->getCupos($seccion['seccion_id']);
            $index += 1;
        }

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }
    }

    private function getCupos($seccionid){
        $cupo_ocupados = $this->seccion->obtenerCupoOcupado([$seccionid]);
        $cupo_seccion = $this->seccion->obtenerCupoSeccion([$seccionid]);

        return intval($cupo_seccion['cupo_maximo']) - intval($cupo_ocupados['estudiantes']);
    }

    public function getHorarioDispo() {
        $header = getallheaders();
    
        $diasString = $header['dias'];
        $diasArray = array_map('trim', explode(',', $diasString));
        $aulaid = $header['aula'];
        $docid = $header['docenteid'];

        $param = [$docid, $this->getPeriodo(), $aulaid];
    
        $result = $this->seccion->horarios($diasArray, $param);
    
        $horario = $this->obtenerHorarios($result);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Horarios obtenidos", "data" => $horario]);
        } else {
            http_response_code(200);
            echo json_encode(["message" => "Horarios no disponibles", "data" => $horario]);
        }
    }

    private function obtenerHorarios($horario) {
        $hora_inicio = [];
        $hora_final = [];
    
        for ($i = 7; $i < 20; $i++) {
            $hora_inicio[] = sprintf('%02d:00', $i);
            $hora_final[] = sprintf('%02d:00', $i + 1);
        }
    
        foreach ($horario as $horas) {
            list($inicio, $final) = explode("-", $horas['horario']);
    
            $inicioInt = (int) explode(":", $inicio)[0];
            $finalInt = (int) explode(":", $final)[0];
    
            for ($i = $inicioInt; $i < $finalInt; $i++) {
                $hora = sprintf('%02d:00', $i);
                if (($key = array_search($hora, $hora_inicio)) !== false) {
                    unset($hora_inicio[$key]);
                }
                if (($key = array_search($hora, $hora_final)) !== false) {
                    unset($hora_final[$key]);
                }
            }
        }
    
        $hora_inicio = array_values($hora_inicio);
        $hora_final = array_values($hora_final);
    
        return ['hora_inicio' => $hora_inicio, 'hora_final' => $hora_final];
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

    public function getPeriodoAca(){

        $result = $this->seccion->obtenerPeriodoAcademico();

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }
    }

    public function updateSeccion() {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
    
        $docenteID = isset($data['docenteid']) && $data['docenteid'] !== '' ? $data['docenteid'] : null;
        $cupos = (isset($data['cupos']) && is_numeric($data['cupos']) && $data['cupos'] !== '') ? intval($data['cupos']) : null;
        $sec = isset($data['seccion_id']) ? $data['seccion_id'] : null;

        if (!$sec) {
            http_response_code(400);
            echo json_encode(["error" => "ID de seccion requerido"]);
            return;
        }
    
        if ($docenteID === null && $cupos !== null) {
            $this->updateCupo($cupos, $sec);

        } elseif ($docenteID !== null && $cupos === null) {
            $this->updateDocente($docenteID, $sec);
    
        } elseif($docenteID !== null & $cupos !== null){
            $this->updateDocAndCupo($docenteID, $cupos, $sec);
        }else {
            http_response_code(400);
            echo json_encode(["error" => "No se proporcionó ningún dato para actualizar"]);
        }
    }

    private function updateDocente($docenteID, $sec){
        
    
            $result = $this->seccion->actualizarDocente([$docenteID, $sec]);
    
            if ($result) {
                
                http_response_code(200);
                echo json_encode(["message" => "Docente actualizado"]);
            
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al actualizar docente"]);
            }
    }

    private function updateCupo($cupos, $sec){

        $cupos_maximo = $this->getCuposMaximo($sec) + $cupos;
        $result = $this->seccion->actualizarCupos([$cupos_maximo, $sec]);

         if ($result) {
            $this->acceptStudentsEspera($sec);
    
            http_response_code(200);
            echo json_encode(["message" => "Cupos actualizados"]);
            
    
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar  cupos"]);
        }
    }

    private function updateDocAndCupo($docenteID, $cupos, $sec){
        
        
        $cupos_maximo = $this->getCuposMaximo($sec) + $cupos;
        $result = $this->seccion->actualizarDocAndCupo([$cupos_maximo, $docenteID, $sec]);
    
        if ($result) {
            $huboInscritos = $this->acceptStudentsEspera($sec);
    
            http_response_code(200);
            if ($huboInscritos) {
                echo json_encode(["message" => "Cupos y docente actualizados. Algunos estudiantes en espera fueron inscritos."]);
            } else {
                echo json_encode(["message" => "Cupos y docente actualizados. No había estudiantes en espera."]);
            }
    
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar docente y cupos"]);
        }
    }
    
    
    private function getCuposMaximo($sec){

        $result = $this->seccion->obtenerCuposMaximos([$sec]);
        return intval($result['cupo_maximo']);

    }

    private function acceptStudentsEspera($sec){

        $result = $this->seccion->obtenerEstudiantesEspera([$sec]);

        if($result){
            foreach ($result as $est) {
                $cupo = $this->getCupos($sec);
            
                if ($cupo > 0) {
                    if (!$this->matriculado($est['id'], $sec)) {
                        $this->mat->create([
                            'estudiante_id' => $est['id'],
                            'seccion_id' => $sec,
                            'fechaInscripcion' => date('y-m-d')
                        ]);
            
                        $this->seccion->eliminarEstudianteEspera([$est['id']]);
                    }
                }
            }

            return true;
        }

        return false;

    }
    
    private function matriculado($estudiante_id, $seccion_id) {
        
        $result = $this->seccion->matricularEstudianteEspera([$estudiante_id, $seccion_id]);
    
        return !empty($result); 
    }

    public function deleteSeccion(){
        $header = getallheaders();
    
        $sec = isset($header['seccionid']) ? $header['seccionid'] : null;
    
        try {

            $resultSec = $this->seccion->eliminarSeccion([$sec]);
            $resultEst = $this->seccion->eliminarMatricula([$sec]);
            $resultEsp = $this->seccion->eliminarEspera([$sec]);
    
            if ($resultSec || $resultEst || $resultEsp) {
                http_response_code(200);
                echo json_encode(["message" => "Sección y registros asociados eliminados exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "No se pudo eliminar la sección o los registros asociados"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error interno del servidor", "details" => $e->getMessage()]);
        }
    }
    
    
}

?>
