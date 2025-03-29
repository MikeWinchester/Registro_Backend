<?php

require_once __DIR__ . "/../models/Cancelacion.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class CancelacionController {
    private $cancelacion;

    public function __construct() {
        $this->cancelacion = new Cancelacion();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    /**
     * retorna las secciones en canceladas de un estudiante
     * 
     * @version 0.1.0
     */
    public function getCanByEstudiante(){

        $header = getallheaders();

        if(!isset($header['estudianteid'])){
            http_response_code(400);
            echo json_encode(['Error'=>'campo estudianteid necesario']);
        }

        $sql = 'SELECT cn.seccion_id, cl.nombre ,periodo_academico, aula, horario, cupo_maximo, ed.edificio, cl.codigo, sec.dias
        FROM tbl_lista_cancelacion as cn
        INNER JOIN tbl_seccion as sec
        ON cn.seccion_id = sec.seccion_id
        INNER JOIN tbl_clase as cl
        ON sec.clase_id = cl.clase_id
        INNER JOIN tbl_aula as al
        ON sec.aula_id = al.aula_id
        INNER JOIN tbl_edificio as ed
        ON cl.edificio_id = ed.edificio_id
        WHERE estudiante_id = ?';

        $estudianteId = $header['estudianteid'];

        $result = $this->cancelacion->customQuery($sql, [$estudianteId]);

        if ($result) {
            echo json_encode(["message" => "Secciones obtenida correctamente", 'data' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener Secciones"]);
        }
    }


    public function createCancelacion($data){

        if(!isset($data['seccion_id']) || !isset($data['estudiante_id'])){
            http_response_code(400);
            echo json_encode(['Error'=>'campo seccionid y estudiante_id necesario']);
        }

        $this->cancelacion->create($data);

        $result = $data;

        if ($result) {
            echo json_encode(["message" => "Secciones cancelada correctamente", 'data' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al cancelar Secciones"]);
        }
    }

}
?>
