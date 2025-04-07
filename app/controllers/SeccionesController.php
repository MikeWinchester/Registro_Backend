<?php

require_once __DIR__ . "/../models/Seccion.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";

class SeccionesController {
    private $seccion;

    public function __construct() {
        $this->seccion = new Seccion();
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

        $result = $this->seccion->customQuery($sql, [$data['docente_id'], $data['horario'], $data['periodo_academico'], $data['dias']]);

        return $result[0]['existe'];
    }

    /**
     * Obtiene las secciones de una clase
     *
     * @version 0.1.1
     */
    public function getSeccionesByClass(){

        $header = getallheaders();

        if(!isset($header['claseid'])){
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
        AND sc.periodo_academico = ?";

        $claseID = $header['claseid'];

        $result = $this->seccion->customQuery($sql, [$claseID, $this->getPeriodo()]);
        
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
    
        if (!isset($header['dias']) || !isset($header['docenteid'])) {
            http_response_code(400);
            echo json_encode(["error" => "Campo dias y docenteid necesario"]);
            return;
        }
    
        $diasString = $header['dias'];
        $diasArray = array_map('trim', explode(',', $diasString));
        $docid = $header['docenteid'];
    
        $sql = "SELECT DISTINCT horario FROM tbl_seccion WHERE docente_id = ? AND periodo_academico = ?";
        $param = [$docid, $this->getPeriodo()];
    
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
    
}

?>
