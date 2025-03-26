<?php

require_once __DIR__ . "/../models/Matricula.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";

class MatriculaController{

    private $matricula;

    public function __construct()
    {
        $this->matricula = new Matricula();
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

        if(!isset($header['SeccionID'])){
            http_response_code(400);
            echo json_encode(["error" => "SeccionID es requerido en el header"]);
            return;
        }
    
        $secID = $header['SeccionID'];

        $sql = "SELECT est.EstudianteID, usr.NombreCompleto, est.NumeroCuenta, est.CorreoInstitucional 
        FROM Matricula as mat
        left join Estudiante as est
        on mat.EstudianteID = est.EstudianteID
        left join Usuario as usr
        on est.UsuarioId = usr.UsuarioID
        where SeccionID = ?
        and EstadoMatricula = 'Activo'";

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
    public function setMatricula(){
        $data = json_decode(file_get_contents("php://input"), true);

        $cupo = $this->matricula->customQuery("SELECT cupo_maximo from tbl_seccion where seccion_id = ?", [$data['seccion_id']]);

        if (isset($cupo[0]['cupo_maximo']) && $cupo[0]['cupo_maximo'] > 0) {
            $seccionID = $data['seccion_id'];

            $result = $this->matricula->create($data);
            if (!$result){
                http_response_code(404);
                echo json_encode(["error" => "No se logro crear matricular"]);
                return;
            }

            $restCupo = $this->matricula->customQueryUpdate("UPDATE tbl_seccion SET cupo_maximo = cupo_maximo - 1 WHERE seccion_id = ?", [$seccionID]);

            echo $restCupo;

            if ($restCupo) {
                http_response_code(200);
                echo json_encode(["message" => "matricula creada", "data" => $data]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "No se logro crear matricular"]);
            }
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

        $sql = "SELECT sc.seccion_id, cl.nombre, al.aula, ed.edificio , cl.UV ,sc.horario, sc.dias
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
    private function getPeriodo(){

        $year = date("Y");
        $mon = date("n");

        $trimestre = ceil($mon / 3);

        switch($trimestre){
            case 1:
                $trimestre = "I";
                break;
                case 2:
                    $trimestre = "II";
                    break;
                    case 3:
                        $trimestre = "III";
                        break;
                    default:
                    $trimestre;
        }
                
    
        return "$year-$trimestre";
        
    }
}

?>
