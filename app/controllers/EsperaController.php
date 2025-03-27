<?php

require_once __DIR__ . "/../models/Espera.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class EsperaController {
    private $espera;

    public function __construct() {
        $this->espera = new Espera();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    /**
     * retorna las secciones en espera de un estudiante
     * 
     * @version 0.1.0
     */
    public function getEspByEstudiante(){

        $header = getallheaders();

        if(!isset($header['estudianteid'])){
            http_response_code(400);
            echo json_encode(['Error'=>'campo estudianteid necesario']);
        }

        $sql = 'SELECT ep.seccion_id, cl.nombre ,periodo_academico, aula, horario, cupo_maximo
        FROM tbl_lista_espera as ep
        INNER JOIN tbl_seccion as sec
        ON ep.seccion_id = sec.seccion_id
        INNER JOIN tbl_clase as cl
        ON sec.clase_id = cl.clase_id
        INNER JOIN tbl_aula as al
        ON sec.aula_id = al.aula_id
        INNER JOIN tbl_edificio as ed
        ON cl.edificio_id = ed.edificio_id
        WHERE estudiante_id = ?';

        $estudianteId = $header['estudianteid'];

        $result = $this->espera->customQuery($sql, [$estudianteId]);

        if ($result) {
            echo json_encode(["message" => "Secciones obtenida correctamente", 'data' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener Secciones"]);
        }
    }


    public function getCupoEsperaBySec(){
        $header = getallheaders();

        if(!isset($header['seccionid'])){
            http_response_code(400);
            echo json_encode(['Error'=>'campo seccionid necesario']);
        }

        $sql = 'SELECT count(cupo) as en_espera
                FROM tbl_lista_espera
                WHERE seccion_id = ?';

        $seccionId = $header['seccionid'];

        $result = $this->espera->customQuery($sql, [$seccionId]);

        if ($result) {
            echo json_encode(["message" => "Secciones obtenida correctamente", 'data' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener Secciones"]);
        }
    }
}
?>
