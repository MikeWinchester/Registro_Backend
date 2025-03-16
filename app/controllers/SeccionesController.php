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
     * Funcion para obtener las secciones de los docentes
     *
     * @param $idDocente id del docente que se quiera obtener las secciones
     *
     * @version 0.1.0
     */
    public function getSecciones($idDocente){

        AuthMiddleware::authMiddleware();

        $sql = "SELECT * FROM Seccion WHERE DocenteID = ? AND PeriodoAcademico = ?";

        $result = $this->seccion->customQuery($sql, [$idDocente, $this->getPeriodo()]);

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
        AuthMiddleware::authMiddleware();

        $sql = "SELECT sec.Asignatura, sec.PeriodoAcademico, sec.Aula, sec.Horario, sec.CupoMaximo, usr.NombreCompleto, usr.Correo
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

        AuthMiddleware::authMiddleware();

        $data = json_decode(file_get_contents("php://input"), true);

        $sql = "SELECT sec.Asignatura, sec.PeriodoAcademico, sec.Aula, sec.Horario, sec.CupoMaximo
        FROM Seccion as sec
        WHERE TRIM(Asignatura) = ? AND PeriodoAcademico = ?
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
     * Funcion para obtener el periodo acadmico actual
     *
     * @return "anio-trimestre" ejemplo: "2021-1"
     * 
     * @version 0.1.0
     */
    private function getPeriodo(){

        $year = date("Y");
        $mon = date("n");

        $trimestre = ceil($mon / 3);
    
        return "$year-$trimestre";
    }
}

?>