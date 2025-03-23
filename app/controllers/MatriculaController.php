<?php

require_once __DIR__ . "/../models/Matricula.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

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

}

?>
