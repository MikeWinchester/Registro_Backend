<?php
require_once __DIR__ . "/../models/Estudiante.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class EstudianteController {
    private $estudiante;
    

    public function __construct() {
        $this->estudiante = new Estudiante();
    }


    /**
     * obtiene el perfil de estudiante
     * 
     * @version 0.1.0
     */
    public function getEspEstudiante(){
        $header = getallheaders();

        if(!isset($header['estudianteid'])){
            http_response_code(400);
            echo json_encode(["error" => "Campo estudianteid necesario"]);
        }

        $sql = "SELECT seccion_id, cl.nombre ,periodo_academico, aula, horario, cupo_maximo
        FROM tbl_lista_espera as ep
        INNER JOIN tbl_seccion as sec
        ON ep.seccion_id = sec.seccion_id
        INNER JOIN tbl_clase as cl
        ON sec.clase_id = cl.clase_id
        INNER JOIN tbl_edificio as ed
        ON cl.edificio_id = ed.edificio_id
        WHERE estudiante_id = ?";

        $estudiante = $header['estudianteid'];

        $result = $this->estudiante->customQuery($sql, [$estudiante]);

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