<?php

require_once __DIR__ . "/../models/Seccion.php";
require_once __DIR__ . "/../models/Matricula.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";

class SeccionesController {
    private $seccion;
    private $mat;

    public function __construct() {
        $this->seccion = new Seccion();
        $this->mat = new Matricula();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    /**
     * Funcion para obtener las secciones de los docentes actuales
     *
     * @version 0.1.2
     */
    public function getSeccionesActuales(){

        #AuthMiddleware::authMiddleware();

        $header = getallheaders();

        if(!isset($header['docenteid'])){
            http_response_code(404);
            echo json_encode(["error" => "Campo DocenteID necestiado"]);
            return;
        }
        
        $docenteid = $header['docenteid'];

        $sql = "SELECT seccion_id, cl.nombre ,periodo_academico, al.aula, horario, cupo_maximo, cl.codigo
        FROM tbl_seccion as sec
        INNER JOIN tbl_clase as cl
        ON sec.clase_id = cl.clase_id
        INNER JOIN tbl_aula as al
        ON sec.aula_id = al.aula_id
        WHERE docente_id = ?
        AND periodo_academico = ?
        ";

        $result = $this->seccion->customQuery($sql, [$docenteid, $this->getPeriodo()]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }

    }

    /**
     * Funcion para obtener las secciones de los docentes
     *
     *
     *
     * @version 0.1.1
     */
    public function getSecciones(){

        #AuthMiddleware::authMiddleware();

        $header = getallheaders();

        if(!isset($header['docenteid'])){
            http_response_code(404);
            echo json_encode(["error" => "Campo DocenteID necestiado"]);
            return;
        }
        
        $docenteid = $header['docenteid'];
        
        $sql = "SELECT seccion_id, cl.nombre ,periodo_academico, aula, horario, cupo_maximo
        FROM tbl_seccion as sec
        INNER JOIN tbl_clase as cl
        ON sec.clase_id = cl.clase_id
        WHERE docente_id = ?";

        $result = $this->seccion->customQuery($sql, [$docenteid]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }

    }


    /**
     * Obtiene informacion de una seccion en especifico
     *
     * @param $idSeccion id de la seccion
     * @version 0.1.1
     */
    public function getSeccion(){

        #AuthMiddleware::authMiddleware();
        $header = getallheaders();

        if(!isset($header['seccionid'])){
            http_response_code(404);
            echo json_encode(["error" => "Campo SeccionID necestiado"]);
            return;
        }
        
        $secID = $header['seccionid'];
        

        $sql = "SELECT cl.nombre, sec.periodo_academico, al.aula, sec.horario, sec.cupo_maximo, usr.nombre_completo, usr.correo
        FROM tbl_seccion as sec
        INNER JOIN tbl_docente as doc
        ON sec.docente_id = doc.docente_id
        INNER JOIN tbl_usuario as usr
        ON doc.usuario_id = usr.usuario_id
        INNER JOIN tbl_clase as cl
        ON sec.clase_id = cl.clase_id
        INNER JOIN tbl_aula as al
        ON sec.aula_id = al.aula_id
        WHERE sec.seccion_id = ?
        ";

        $result = $this->seccion->customQuery($sql, [$secID]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Seccion encontrada", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Seccion no disponible"]);
        }
    }

    /**
     * Obtiene las secciones disponibles de una asignacion en especifica del periodo actual
     *
     *
     *
     * @version 0.1.1
     */
    public function getSeccionAsig(){

        #AuthMiddleware::authMiddleware();

        $header = getallheaders();

        if(!isset($header['ClaseID'])){
            http_response_code(404);
            echo json_encode(["error" => "Campo ClaseID necestiado"]);
            return;
        }
        
        $ClaseID = $header['ClaseID'];

        $sql = "SELECT cl.Nombre, sec.PeriodoAcademico, sec.Aula, sec.Horario, sec.CupoMaximo
        FROM Seccion as sec
        LEFT JOIN Clase as cl
        ON sec.ClaseID = cl.ClaseID
        WHERE sec.ClaseID = ?
        AND PeriodoAcademico = ?
        ";

        $result = $this->seccion->customQuery($sql, [$ClaseID, $this->getPeriodo()]);
        
        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }
    }
    
    
    /**
     * Funcion para contar las secciones asignadas a un docente
     *
     * @version 0.1.1
     */
    public function getSeccionCount(){

        #AuthMiddleware::authMiddleware();

        $header = getallheaders();

        if(!isset($header['docenteid'])){
            http_response_code(404);
            echo json_encode(["error" => "Campo DocenteID necestiado"]);
            return;
        }
        
        $docenteid = $header['docenteid'];


        $sql = "SELECT count(1) as cantidad
        FROM tbl_seccion
        WHERE docente_id = ?
        AND periodo_academico = ?
        ";

        $result = $this->seccion->customQuery($sql, [$docenteid, $this->getPeriodo()]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }
    }

    /**
     * Crea secciones de clases
     *
     * @version 0.1.0
     */
    public function createSeccion() {
        header('Content-Type: application/json');
    
        $data = json_decode(file_get_contents("php://input"), true);

        $centroID = $this->getCentroByJefe($data['jefeID']);

        unset($data['jefeID']);
        $data['periodo_academico'] = $this->getPeriodo();

        $data['centro_regional_id'] = $centroID[0]['id'];
    
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

    private function getCentroByJefe($jefe){
     
        $sql = 'SELECT centro_regional_id AS id
                FROM tbl_jefe AS jf
                INNER JOIN tbl_docente AS dc
                ON jf.docente_id = dc.docente_id
                WHERE jefe_id = ?';

        return $this->seccion->customQuery($sql, [$jefe]);
        
    }
    

    /**
     * Revisa si ya existe una seccion dentro de los parametros de un docente
     * 
     * @version 0.1.0
     */
    private function validateSec($data){

        $sql = "SELECT COUNT(1) AS existe
                FROM tbl_seccion
                WHERE docente_id = ?
                AND horario = ?
                AND periodo_academico = ?
                AND (
                    dias LIKE CONCAT('%', ?, '%')
                )
                ";

        $result = $this->seccion->customQuery($sql, [$data['docente_id'], $data['horario'], $this->getPeriodo(), $data['dias']]);

        return $result[0]['existe'];
    }

    /**
     * Obtiene las secciones de una clase
     *
     * @version 0.1.1
     */
    public function getSeccionesByClassEstu(){

        $header = getallheaders();

        if(!isset($header['claseid']) || !isset($header['estudianteid'])){
            http_response_code(400);
            echo json_encode(["error" => "Campo claseid necesario"]);
        }

        $sql = "SELECT sc.seccion_id, us.nombre_completo, sc.horario, al.aula, sc.cupo_maximo
        FROM tbl_seccion AS sc
        INNER JOIN tbl_docente AS dc
        ON sc.docente_id = dc.docente_id
        INNER JOIN tbl_usuario AS us
        on dc.usuario_id = us.usuario_id
        INNER JOIN tbl_aula as al
        ON sc.aula_id = al.aula_id
        WHERE sc.clase_id = ?
        AND sc.periodo_academico = ?
        AND sc.centro_regional_id = ?";

        $claseID = $header['claseid'];
        $estu = $header['estudianteid'];
        $centro = $this->getCentroByEstu($estu);

        $result = $this->seccion->customQuery($sql, [$claseID, $this->getPeriodo(), $centro[0]['id']]);
        
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

    public function getSeccionesByClass(){

        $header = getallheaders();

        $sql = "SELECT sc.seccion_id, us.nombre_completo, sc.horario, al.aula, sc.cupo_maximo
        FROM tbl_seccion AS sc
        INNER JOIN tbl_docente AS dc
        ON sc.docente_id = dc.docente_id
        INNER JOIN tbl_usuario AS us
        on dc.usuario_id = us.usuario_id
        INNER JOIN tbl_aula as al
        ON sc.aula_id = al.aula_id
        WHERE sc.clase_id = ?
        AND sc.periodo_academico = ?
        AND sc.centro_regional_id = ?";

        $claseID = $header['claseid'];
        $jefe = $header['jefeid'];
        $centro = $this->getCentroByJefe($jefe);

        $result = $this->seccion->customQuery($sql, [$claseID, $this->getPeriodo(), $centro[0]['id']]);
        
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

    private function getCentroByEstu($estu){
        $sql = 'SELECT centro_regional_id AS id
                FROM tbl_estudiante as et
                WHERE estudiante_id = ?';

        return $this->seccion->customQuery($sql, [$estu]);
    }

    public function getSeccionesByClassDoc(){

        $header = getallheaders();

        if(!isset($header['claseid']) || !isset($header['docenteid'])){
            http_response_code(400);
            echo json_encode(["error" => "Campo claseid y docenteid necesario"]);
        }

        $sql = "SELECT sc.seccion_id, us.nombre_completo, sc.horario, al.aula, sc.cupo_maximo
        FROM tbl_seccion AS sc
        INNER JOIN tbl_docente AS dc
        ON sc.docente_id = dc.docente_id
        INNER JOIN tbl_usuario AS us
        on dc.usuario_id = us.usuario_id
        INNER JOIN tbl_aula as al
        ON sc.aula_id = al.aula_id
        WHERE sc.clase_id = ?
        AND sc.docente_id = ?
        AND sc.periodo_academico = ?";

        $claseID = $header['claseid'];
        $docID = $header['docenteid'];

        $result = $this->seccion->customQuery($sql, [$claseID, $docID,$this->getPeriodo()]);
        
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
        $cupo_ocupados = $this->seccion->customQuery("SELECT count(1) as estudiantes FROM tbl_matricula WHERE seccion_id = ?", [$seccionid]);
        $cupo_seccion = $this->seccion->customQuery("SELECT cupo_maximo FROM tbl_seccion WHERE seccion_id = ?", [$seccionid]);

        return intval($cupo_seccion[0]['cupo_maximo']) - intval($cupo_ocupados[0]['estudiantes']);
    }

    public function getHorarioDispo() {
        $header = getallheaders();
    
        if (!isset($header['dias']) || !isset($header['docenteid']) || !isset($header['aula'])) {
            http_response_code(400);
            echo json_encode(["error" => "Campo dias y docenteid necesario"]);
            return;
        }
    
        $diasString = $header['dias'];
        $diasArray = array_map('trim', explode(',', $diasString));
        $aulaid = $header['aula'];
        $docid = $header['docenteid'];
    
        $sql = "SELECT DISTINCT horario FROM tbl_seccion WHERE (docente_id = ? AND periodo_academico = ?) OR aula_id = ?";
        $param = [$docid, $this->getPeriodo(), $aulaid];
    
        if (count($diasArray) > 0) {
            $sql .= " AND (";
    
            $conditions = [];
            foreach ($diasArray as $dia) {
                $conditions[] = "dias LIKE ?";
                $param[] = "%$dia%";
            }
    
            $sql .= implode(" OR ", $conditions) . ")";
        }

        $result = $this->seccion->customQuery($sql, $param);
    
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

        $sql = "SELECT DISTINCT periodo_academico
        FROM tbl_seccion";

        $result = $this->seccion->customQuery($sql);

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
        $sql = 'UPDATE tbl_seccion SET docente_id = ? WHERE seccion_id = ?';
    
            $result = $this->seccion->customQueryUpdate($sql, [$docenteID, $sec]);
    
            if ($result) {
                
                http_response_code(200);
                echo json_encode(["message" => "Docente actualizado"]);
            
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error al actualizar docente"]);
            }
    }

    private function updateCupo($cupos, $sec){
        $sql = 'UPDATE tbl_seccion SET cupo_maximo = ? WHERE seccion_id = ?';
    
        $cupos_maximo = $this->getCuposMaximo($sec) + $cupos;
        $result = $this->seccion->customQueryUpdate($sql, [$cupos_maximo, $sec]);

        if ($result) {
            $huboInscritos = $this->acceptStudentsEspera($sec);
    
            http_response_code(200);
            if($huboInscritos) {
                echo json_encode(["message" => "Cupos actualizados"]);
            } else {
                echo json_encode(["message" => "Cupos actualizados"]);
            }
    
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar  cupos"]);
        }
    }

    private function updateDocAndCupo($docenteID, $cupos, $sec){
        $sql = 'UPDATE tbl_seccion SET cupo_maximo = ?, docente_id = ? WHERE seccion_id = ?';
        
        $cupos_maximo = $this->getCuposMaximo($sec) + $cupos;
        $result = $this->seccion->customQueryUpdate($sql, [$cupos_maximo, $docenteID, $sec]);
    
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

        $sql = 'SELECT cupo_maximo
                FROM tbl_seccion
                WHERE seccion_id = ?';

        $result = $this->seccion->customQuery($sql, [$sec]);
        return intval($result[0]['cupo_maximo']);

    }

    private function acceptStudentsEspera($sec){

        $sql = "SELECT lep.estudiante_id AS id
                FROM tbl_lista_espera AS lep
                WHERE seccion_id = ?
                ORDER BY (lista_espera_id)";

        $sqlDel = "DELETE FROM tbl_lista_espera WHERE estudiante_id = ?";

        $result = $this->seccion->customQuery($sql, [$sec]);

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
            
                        $this->seccion->customQueryUpdate($sqlDel, [$est['id']]);
                    }
                }
            }

            return true;
        }

        return false;

    }
    
    private function matriculado($estudiante_id, $seccion_id) {
        $sql = "SELECT 1 FROM tbl_matricula WHERE estudiante_id = ? AND seccion_id = ?";
        $result = $this->seccion->customQuery($sql, [$estudiante_id, $seccion_id]);
    
        return !empty($result); 
    }

    public function deleteSeccion(){
        $header = getallheaders();
    
        $sec = isset($header['seccionid']) ? $header['seccionid'] : null;
    
        if ($sec === null) {
            http_response_code(400);
            echo json_encode(["error" => "ID de sección requerido"]);
            return;
        }
    
        try {

            $sqlEst = 'DELETE FROM tbl_matricula WHERE seccion_id = ?';
            $resultEst = $this->seccion->customQueryUpdate($sqlEst, [$sec]);

            $sqlEsp = 'DELETE FROM tbl_lista_espera WHERE seccion_id = ?';
            $resultEsp = $this->seccion->customQueryUpdate($sqlEsp, [$sec]);
    
            $sqlSec = 'DELETE FROM tbl_seccion WHERE seccion_id = ?';
            $resultSec = $this->seccion->customQueryUpdate($sqlSec, [$sec]);
    
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
