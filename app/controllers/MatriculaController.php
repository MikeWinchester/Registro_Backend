<?php

require_once __DIR__ . "/../models/Matricula.php";
require_once __DIR__ . "/../controllers/CancelacionController.php";
require_once __DIR__ . "/../models/Espera.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";

class MatriculaController{

    private $matricula;
    private $espera;
    private $cancelacion;

    public function __construct()
    {
        $this->espera = new Espera();
        $this->matricula = new Matricula();
        $this->cancelacion = new CancelacionController();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    /**
     * Funcion para obtener los estudiantes matriculados en una seccion
     *
     * 
     *
     * @version 0.1.1
     */
    public function getEstudiantes(){

        #AuthMiddleware::authMiddleware();

        $header = getallheaders();

        if(!isset($header['seccionid'])){
            http_response_code(400);
            echo json_encode(["error" => "seccionid es requerido en el header"]);
            return;
        }
    
        $secID = $header['seccionid'];

        $sql = "SELECT est.estudiante_id, usr.nombre_completo, usr.numero_cuenta, est.correo
        FROM tbl_matricula as mat
        left join tbl_estudiante as est
        on mat.estudiante_id = est.estudiante_id
        left join tbl_usuario as usr
        on est.usuario_id = usr.usuario_id
        where seccion_id = ?";

        $result = $this->matricula->customQuery($sql, [$secID]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Estudiantes encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Estudiantes no disponibles"]);
        }


    }

    public function getEstudiantesNotas(){

        #AuthMiddleware::authMiddleware();

        $header = getallheaders();

        if(!isset($header['seccionid'])){
            http_response_code(400);
            echo json_encode(["error" => "seccionid es requerido en el header"]);
            return;
        }
    
        $secID = $header['seccionid'];

        $sql = "SELECT est.estudiante_id, usr.nombre_completo, usr.numero_cuenta, est.correo
        FROM tbl_matricula as mat
        left join tbl_estudiante as est
        on mat.estudiante_id = est.estudiante_id
        left join tbl_usuario as usr
        on est.usuario_id = usr.usuario_id
        LEFT JOIN tbl_notas as nt
        on mat.estudiante_id = nt.estudiante_id
        where mat.seccion_id = ?
        AND nt.estudiante_id is null";

        $result = $this->matricula->customQuery($sql, [$secID]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Estudiantes encontrados", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Estudiantes no disponibles"]);
        }


    }

    /**
     * Crear la matricula de un estudiante
     * 
     * @version 0.1.2
     */
    public function setMatricula() {
        $data = json_decode(file_get_contents("php://input"), true);

        $result = $this->checkClase($data);
    
        
        if (intval($result[0]['existe']) == 0) {

            $result = $this->cumpleHorario($data);
            
            if(intval($result[0]['existe'] == 0)){

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

    private function checkClase($data){

        return $this->matricula->customQuery(
            "SELECT COUNT(1) AS existe
             FROM tbl_matricula AS mt
             INNER JOIN tbl_lista_espera AS ep ON mt.seccion_id = ep.seccion_id
             INNER JOIN tbl_seccion as sc ON mt.seccion_id = sc.seccion_id
             WHERE (mt.estudiante_id = ? AND sc.clase_id = ?)
             OR ep.estudiante_id = ?",
            [$data['estudiante_id'], $data['clase_id'], $data['estudiante_id']]
        );
    }

    private function cumpleHorario($data){
        
        $sql = "WITH tbl_horario AS (
            SELECT horario, dias
            FROM tbl_seccion 
            WHERE seccion_id = ?
        )
        SELECT COUNT(1) AS existe
        FROM tbl_matricula AS mat
        INNER JOIN tbl_seccion AS sec
            ON mat.seccion_id = sec.seccion_id
        INNER JOIN tbl_horario AS hr
            ON sec.horario = hr.horario 
            AND sec.dias LIKE CONCAT('%', hr.dias, '%') 
        WHERE mat.estudiante_id = ?
            AND sec.periodo_academico = ?";

        return $this->matricula->customQuery($sql, [$data['seccion_id'], $data['estudiante_id'], $this->getPeriodo()]);
    }

    private function revisionCupos($data){
        $cupo_ocupados = $this->matricula->customQuery("SELECT count(1) as estudiantes FROM tbl_matricula WHERE seccion_id = ?", [$data['seccion_id']]);
        $cupo_seccion = $this->matricula->customQuery("SELECT cupo_maximo FROM tbl_seccion WHERE seccion_id = ?", [$data['seccion_id']]);

        $cupos_disponibles = (intval($cupo_seccion[0]['cupo_maximo']) - intval($cupo_ocupados[0]['estudiantes']));
        
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
    
    /**
     * Revisa que cumpla los requisitos para matricular
     * 
     * @version 0.1.0
     */
    public function cumpleRequisito(){
        $header = getallheaders();

        if(!isset($header['estudianteid']) || !isset($header['claseid'])){
            http_response_code(400);
            echo json_encode(["error" => "estudianteid y claseid necesario"]);
            return;
        }

        $est = $header['estudianteid'];
        $cla = $header['claseid'];

        $sql = "WITH tbl_apr AS (
                    SELECT sc.clase_id
                    FROM tbl_notas AS nt
                    INNER JOIN tbl_seccion AS sc
                    ON nt.seccion_id = sc.seccion_id
                    WHERE nt.estudiante_id = ?
                    AND nt.observacion_id = 1
                ),
                requisitos AS (
                    SELECT count(1) AS requisitos
                    FROM tbl_clase_requisito AS cr
                    WHERE cr.clase_id = ?
                )
                SELECT
                    CASE
                        WHEN r.requisitos = 0 THEN 1
                        ELSE (
                            SELECT count(1)
                            FROM tbl_clase_requisito AS cr
                            INNER JOIN tbl_apr AS ap
                            ON cr.requisito_clase_id = ap.clase_id
                            WHERE cr.clase_id = ?
                        )
                    END AS cumple
                FROM requisitos r;
                ";

        $result = $this->matricula->customQuery($sql, [$est, $cla, $cla]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Cumple los requisitos", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No cumple los requisitos"]);
        }

        
    }

    /**
     * Obtener matricula del estudiante
     * 
     * @version 0.1.0
     */
    public function getMatriculaEst(){

        $header = getallheaders();

        if(!isset($header['estudianteid'])){
            http_response_code(400);
            echo json_encode(["error" => "estudianteid es requerido en el header"]);
            return;
        }
    
        $estId = $header['estudianteid'];

        $sql = "SELECT sc.seccion_id, cl.codigo , cl.nombre, al.aula, ed.edificio , cl.UV ,sc.horario, sc.dias, sc.periodo_academico
                FROM tbl_matricula as mt
                INNER JOIN tbl_seccion as sc
                ON mt.seccion_id = sc.seccion_id
                INNER JOIN tbl_aula as al
                ON sc.aula_id = al.aula_id
                INNER JOIN tbl_clase as cl
                ON sc.clase_id = cl.clase_id
                INNER JOIN tbl_edificio as ed
                ON cl.edificio_id = ed.edificio_id
                WHERE mt.estudiante_id = ?
                AND sc.periodo_academico = ?";

        $result = $this->matricula->customQuery($sql, [$estId, $this->getPeriodo()]);

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

        if(!isset($header['estudianteid'])){
            http_response_code(400);
            echo json_encode(["error" => "estudianteid es requerido en el header"]);
            return;
        }
    
        $estId = $header['estudianteid'];

        $sql = "SELECT cl.nombre, nt.calificacion, cl.codigo, cl.UV, mt.fechaInscripcion
                FROM tbl_matricula as mt
                INNER JOIN tbl_seccion as sc
                ON mt.seccion_id = sc.seccion_id
                INNER JOIN tbl_clase as cl
                ON sc.clase_id = cl.clase_id
                INNER JOIN tbl_notas as nt
                ON sc.seccion_id = nt.seccion_id
                WHERE mt.estudiante_id = ?
                ";

        $result = $this->matricula->customQuery($sql, [$estId, $this->getPeriodo()]);

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

        if(!isset($header['estudianteid']) || !isset($header['seccionid'])){
            http_response_code(400);
            echo json_encode(["error" => "estudianteid es requerido en el header"]);
            return;
        }
    
        $estId = $header['estudianteid'];
        $secId = $header['seccionid'];

        $sqlMat = "DELETE FROM tbl_matricula
                WHERE estudiante_id = ?
                AND seccion_id = ?";


        $sqlSec = "UPDATE tbl_seccion SET cupo_maximo = cupo_maximo + 1 WHERE seccion_id = ?";

        $resultMAT = $this->matricula->customQueryUpdate($sqlMat, [$estId, $secId]);

        if ($resultMAT) {
            $resultSec = $this->matricula->customQueryUpdate($sqlSec, [$secId]);
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

    public function permitirMatriculaEstu() {
        $header = getallheaders();
        $estudianteId = $header['estudianteid'] ?? null;
    
        $sql = "SELECT * FROM tbl_info_matricula WHERE estado_matricula_id = 1 LIMIT 1";
        $rango = $this->matricula->customQuery($sql);
    
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
        $sql = "SELECT sum(nt.calificacion * UV)/sum(UV) AS promedio
                FROM tbl_notas AS nt
                INNER JOIN tbl_seccion AS sc ON nt.seccion_id = sc.seccion_id
                INNER JOIN tbl_clase AS cl ON sc.clase_id = cl.clase_id
                WHERE nt.estudiante_id = ?";
    
        $res = $this->matricula->customQuery($sql, [$estudianteid]);
        return $res && isset($res[0]['promedio']) ? floatval($res[0]['promedio']) : null;
    }
    
    
    
}



?>
