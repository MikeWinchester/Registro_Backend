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
    public function getEstudiante(){
        $header = getallheaders();

        if(!isset($header['estudianteid'])){
            http_response_code(400);
            echo json_encode(["error" => "Campo estudianteid necesario"]);
        }

        $sql = "SELECT usr.nombre_completo, usr.numero_cuenta, est.correo
                FROM tbl_estudiante as est
                INNER JOIN tbl_usuario as usr
                ON est.usuario_id = usr.usuario_id
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