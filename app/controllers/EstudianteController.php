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

        /**
         * obtiene el perfil de estudiante
         * 
         * @version 0.1.0
         */
        public function getEstudiante(){
            $header = getallheaders();
    
            if(!isset($header['estudianteid'])){
                http_response_code(400);
                echo json_encode(["error" => "Campo estudianteid necesario"]);
            }
    
            $sql = "SELECT nombre_completo, numero_cuenta, nombre_carrera, nombre_centro
            FROM tbl_usuario AS usr
            INNER JOIN tbl_estudiante as est
            ON usr.usuario_id = est.usuario_id
            INNER JOIN tbl_carrera as cr
            ON est.carrera_id = cr.carrera_id
            INNER JOIN tbl_centro_regional as tcr
            ON tcr.centro_regional_id = est.centro_regional_id
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

        public function getEstudianteByCuenta(){
            $header = getallheaders();

            if(!isset($header['cuenta'])){
                http_response_code(400);
                echo json_encode(["error" => "Campo cuenta necesario"]);
            }

            $sql = "SELECT nombre_completo, numero_cuenta, nombre_carrera, nombre_centro
            FROM tbl_usuario AS usr
            INNER JOIN tbl_estudiante as est
            ON usr.usuario_id = est.usuario_id
            INNER JOIN tbl_carrera as cr
            ON est.carrera_id = cr.carrera_id
            INNER JOIN tbl_centro_regional as tcr
            ON tcr.centro_regional_id = est.centro_regional_id
            WHERE usr.numero_cuenta = ?";

            $estudiante = $header['cuenta'];
            

            $result = $this->estudiante->customQuery($sql, [$estudiante]);

            if ($result) {
                http_response_code(200);
                echo json_encode(["message" => "Estudiante encontrado", "data" => $result]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Estudiante  no encontrado"]);
            }
        }
}
?>