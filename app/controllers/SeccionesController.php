<?php

require_once __DIR__ . "/../models/Seccion.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class SeccionesController {
    private $seccion;

    public function __construct() {
        $this->seccion = new Seccion();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    /**
     * Funcion para obtener las secciones de los docentes actuales
     *
     * 
     *
     * @version 0.1.1
     */
    public function getSeccionesActuales(){

        #AuthMiddleware::authMiddleware();

        $data = json_decode(file_get_contents("php://input"), true);
        
        $sql = "SELECT SeccionID, cl.Nombre ,PeriodoAcademico, Aula, Horario, CupoMaximo
        FROM Seccion as sec
        INNER JOIN Clase as cl
        ON sec.ClaseID = cl.ClaseID
        WHERE DocenteID = ?
        AND PeriodoAcademico = ?
        ";

        $result = $this->seccion->customQuery($sql, [$data['DocenteID'], $this->getPeriodo()]);

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

        $data = json_decode(file_get_contents("php://input"), true);
        
        $sql = "SELECT SeccionID, cl.Nombre ,PeriodoAcademico, Aula, Horario, CupoMaximo
        FROM Seccion as sec
        INNER JOIN Clase as cl
        ON sec.ClaseID = cl.ClaseID
        WHERE DocenteID = ?
        ";

        $result = $this->seccion->customQuery($sql, [$data['DocenteID']]);

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
     * @version 0.1.0
     */
    public function getSeccion($idSeccion){

        #AuthMiddleware::authMiddleware();

        $sql = "SELECT cl.Nombre, sec.PeriodoAcademico, sec.Aula, sec.Horario, sec.CupoMaximo, usr.NombreCompleto, usr.Correo
        FROM Seccion as sec
        INNER JOIN Docente as doc
        ON sec.DocenteID = doc.DocenteID
        INNER JOIN Usuario as usr
        ON doc.UsuarioID = usr.UsuarioID
        WHERE sec.SeccionID = ?
        ";

        $result = $this->seccion->customQuery($sql, [$idSeccion]);

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
     * @param json {'Asigntura' : 'Contabilidad'}
     *
     * @version 0.1.0
     */
    public function getSeccionAsig(){

        #AuthMiddleware::authMiddleware();

        $data = json_decode(file_get_contents("php://input"), true);

        $sql = "SELECT sec.Asignatura, sec.PeriodoAcademico, sec.Aula, sec.Horario, sec.CupoMaximo
        FROM Seccion as sec
        WHERE ClaseID = ?
        AND PeriodoAcademico = ?
        ";

        $result = $this->seccion->customQuery($sql, [$data['Asignatura'], $this->getPeriodo()]);

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
     * @version 0.1.0
     */
    public function getSeccionCount(){

        #AuthMiddleware::authMiddleware();

        $data = json_decode(file_get_contents("php://input"), true);

        $sql = "SELECT count(1) as cantidad
        FROM Seccion
        WHERE DocenteID = ?
        AND PeriodoAcademico = ?
        ";

        $result = $this->seccion->customQuery($sql, [$data['DocenteID'], $this->getPeriodo()]);

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
    public function createSeccion(){

        #AuthMiddleware::authMiddleware();

        $data = json_decode(file_get_contents("php://input"), true);

        if ($this->seccion->create($data)) {
            http_response_code(200);
            echo json_encode(["message" => "Seccion creada", "data" => $data]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se logro crear la seccion"]);
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