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
            echo json_encode(["message" => "Estudiantes encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Estudiantes no disponibles"]);
        }


    }

    /**
     * Crear la matricula de un estudiante
     */
    public function setMatricula() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        $result = $this->matricula->customQuery(
            "SELECT COUNT(1) AS existe
             FROM tbl_matricula AS mt
             INNER JOIN tbl_lista_espera AS ep ON mt.seccion_id = ep.seccion_id
             INNER JOIN tbl_seccion as sc ON mt.seccion_id = sc.seccion_id
             WHERE (mt.estudiante_id = ? AND sc.clase_id = ?)
             OR ep.estudiante_id = ?",
            [$data['estudiante_id'], $data['clase_id'], $data['estudiante_id']]
        );
    
        
        if (intval($result[0]['existe']) == 0) {
            $cupo = $this->matricula->customQuery("SELECT cupo_maximo FROM tbl_seccion WHERE seccion_id = ?", [$data['seccion_id']]);
            $seccionID = $data['seccion_id'];
    
            if (isset($cupo[0]['cupo_maximo']) && $cupo[0]['cupo_maximo'] > 0) {
                unset($data['clase_id']);
                $result = $this->matricula->create($data);
    
                if (!$result) {
                    http_response_code(400);
                    echo json_encode(["error" => "No se logró matricular"]);
                    return;
                }
    
                $restCupo = $this->matricula->customQueryUpdate("UPDATE tbl_seccion SET cupo_maximo = cupo_maximo - 1 WHERE seccion_id = ?", [$seccionID]);
    
                if ($restCupo) {
                    http_response_code(200);
                    echo json_encode(["message" => "Matrícula creada", "data" => $data]);
                } else {
                    http_response_code(400);
                    echo json_encode(["error" => "No se logró actualizar el cupo"]);
                }
            } else {
                
                $esperaData = ['seccion_id' => $data['seccion_id'], 'estudiante_id' => $data['estudiante_id']];
                $result = $this->espera->create($esperaData);
    
                if ($result) {
                    http_response_code(200);
                    echo json_encode(["message" => "Se agregó a lista de espera", "data" => $esperaData]);
                } else {
                    http_response_code(400);
                    echo json_encode(["error" => "No se logró agregar en espera"]);
                }
            }
        } else {
            http_response_code(200);
            echo json_encode(["message" => "El estudiante ya está matriculado", "data" => 'Clase Matriculada']);
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
    
}

?>
