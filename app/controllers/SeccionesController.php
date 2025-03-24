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
     * @version 0.1.2
     */
    public function getSeccionesActuales(){

        #AuthMiddleware::authMiddleware();

        $header = getallheaders();

        if(!isset($header['DocenteID'])){
            http_response_code(404);
            echo json_encode(["error" => "Campo DocenteID necestiado"]);
            return;
        }
        
        $docenteid = $header['DocenteID'];

        $sql = "SELECT SeccionID, cl.Nombre ,PeriodoAcademico, Aula, Horario, CupoMaximo
        FROM Seccion as sec
        INNER JOIN Clase as cl
        ON sec.ClaseID = cl.ClaseID
        WHERE DocenteID = ?
        AND PeriodoAcademico = ?
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

        if(!isset($header['DocenteID'])){
            http_response_code(404);
            echo json_encode(["error" => "Campo DocenteID necestiado"]);
            return;
        }
        
        $docenteid = $header['DocenteID'];
        
        $sql = "SELECT SeccionID, cl.Nombre ,PeriodoAcademico, Aula, Horario, CupoMaximo
        FROM Seccion as sec
        INNER JOIN Clase as cl
        ON sec.ClaseID = cl.ClaseID
        WHERE DocenteID = ?
        ";

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

        if(!isset($header['SeccionID'])){
            http_response_code(404);
            echo json_encode(["error" => "Campo SeccionID necestiado"]);
            return;
        }
        
        $secID = $header['SeccionID'];
        

        $sql = "SELECT cl.Nombre, sec.PeriodoAcademico, sec.Aula, sec.Horario, sec.CupoMaximo, usr.NombreCompleto, usr.Correo
        FROM Seccion as sec
        INNER JOIN Docente as doc
        ON sec.DocenteID = doc.DocenteID
        INNER JOIN Usuario as usr
        ON doc.UsuarioID = usr.UsuarioID
        INNER JOIN Clase as cl
        ON sec.ClaseID = cl.ClaseID
        WHERE sec.SeccionID = ?
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

        if(!isset($header['DocenteID'])){
            http_response_code(404);
            echo json_encode(["error" => "Campo DocenteID necestiado"]);
            return;
        }
        
        $docenteid = $header['DocenteID'];


        $sql = "SELECT count(1) as cantidad
        FROM Seccion
        WHERE DocenteID = ?
        AND PeriodoAcademico = ?
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
     * Obtiene las secciones de una clase
     *
     * @version 0.1.0
     */
    public function getSeccionesByClass(){
        $header = getallheaders();

        if(!isset($header)){
            http_response_code(400);
            echo json_encode(["error" => "Campo ClaseID necesario"]);
        }

        $sql = "SELECT sc.SeccionID, us.NombreCompleto, sc.Horario, sc.Aula, sc.CupoMaximo
        FROM Seccion AS sc
        INNER JOIN Docente AS dc
        ON sc.DocenteID = dc.DocenteID
        INNER JOIN Usuario AS us
        on dc.UsuarioID = us.UsuarioID
        WHERE sc.ClaseID = ?
        AND sc.PeriodoAcademico = ?";

        $claseID = $header['claseid'];

        $result = $this->seccion->customQuery($sql, [$claseID, $this->getPeriodo()]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
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
