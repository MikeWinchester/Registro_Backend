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

        $sql = 'SELECT ep.seccion_id, cl.nombre ,periodo_academico, aula, horario, cupo_maximo, ed.edificio, cl.codigo, sec.dias
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


    /**
     * Obtener cupo en espera
     * 
     * @version 0.1.0
     */
    public function getCupoEsperaBySec(){
        $header = getallheaders();

        if(!isset($header['seccionid'])){
            http_response_code(400);
            echo json_encode(['Error'=>'campo seccionid necesario']);
        }

        $sql = 'SELECT count(1) as en_espera
                FROM tbl_lista_espera
                WHERE seccion_id = ?';

        $seccionId = $header['seccionid'];

        $result = $this->espera->customQuery($sql, [$seccionId]);

        if ($result) {
            echo json_encode(["message" => "Secciones obtenidas correctamente", "data" => $result]);
        } else {
            echo json_encode(["message" => "Secciones en espera inicia", "data" => [['en_espera' => 0]]]);
        }
    }
    
    /**
     * Eliminar de espera una seccion
     * 
     * @version 0.1.0
     */
    public function delEspera(){

        $header = getallheaders();

        if(!isset($header['seccionid']) || !isset($header['estudianteid'])){
            http_response_code(400);
            echo json_encode(['Error'=>'campo seccionid y estudianteid necesario']);
        }

        $sql = 'DELETE FROM tbl_lista_espera
                WHERE seccion_id = ?
                AND estudiante_id = ?';

        $seccionId = $header['seccionid'];
        $estudianteId = $header['estudianteid'];

        $result = $this->espera->customQueryUpdate($sql, [$seccionId, $estudianteId]);

        if ($result) {
            echo json_encode(["message" => "Secciones obtenida correctamente", 'data' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener Secciones"]);
        }
    }

    /**
     * obtiene estudiantes en espera segun departamento
     * 
     * @version 0.1.0
     */
    public function getEstEsperaDep(){
        $header = getallheaders();

        if(!isset($header['departamentoid'])){
            http_response_code(400);
            echo json_encode(['Error'=>'campo departamentoid necesario']);
        }

        $sql = 'SELECT cl.codigo, cl.nombre, sc.horario, est.estudiante_id, al.aula, ed.edificio, us.numero_cuenta, sc.periodo_academico, sc.seccion_id
                FROM tbl_lista_espera as lep
                INNER JOIN tbl_seccion as sc
                ON lep.seccion_id = sc.seccion_id
                INNER JOIN tbl_clase as cl
                ON sc.clase_id = cl.clase_id
                INNER JOIN tbl_aula as al
                ON sc.aula_id = al.aula_id
                INNER JOIN tbl_edificio as ed
                ON al.edificio_id = ed.edificio_id
                INNER JOIN tbl_estudiante as est
                ON lep.estudiante_id = est.estudiante_id
                INNER JOIN tbl_usuario as us
                ON est.usuario_id = us.usuario_id
                WHERE cl.departamento_id = ?';

        $departamentoid = $header['departamentoid'];

        $result = $this->espera->customQuery($sql, [$departamentoid]);

        if ($result) {
            echo json_encode(["message" => "Lista de espera obtenida correctamente", "data" => $result]);
        } else {
            echo json_encode(["message" => "Lista de espera no obtenida"]);
        }
    }
    
}
?>
