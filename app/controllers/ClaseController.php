<?php
require_once __DIR__ . "/../models/Clase.php";
require_once __DIR__ . "/../controllers/CarreraController.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class ClaseController {
    private $clase;
    private $carrera;

    public function __construct() {
        $this->clase = new Clase();
        $this->carrera = new CarreraController();
    }


    /**
     * Obtiene todos las clases por Area
     *
     * @version 0.1.1
     */
    public function getClasesByArea(){

        #AuthMiddleware::authMiddleware();

        $header = array_change_key_case(getallheaders(), CASE_LOWER);
        if (!isset($header['areaid'])) {
            http_response_code(400);
            echo json_encode(["error" => "areaid es requerido en el header"]);
            return;
        }

        $depID = $header['areaid']; // Accede en minúsculas


        $sql = "SELECT cl.clase_id, cl.nombre, cl.codigo, cl.UV
        FROM tbl_clase AS cl
        WHERE cl.departamento_id = ?";

        $result = $this->clase->customQuery($sql, [$depID]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "clases obtenidas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Clases no obtenidas"]);
        }
    }

    public function getClasesByAreaEstu(){

        #AuthMiddleware::authMiddleware();

        $header = array_change_key_case(getallheaders(), CASE_LOWER);
        if (!isset($header['areaid']) || !isset($header['estudianteid'])) {
            http_response_code(400);
            echo json_encode(["error" => "areaid y estudianteid es requerido en el header"]);
            return;
        }

        $depID = $header['areaid'];
        $est = $header['estudianteid'];

        $carID = $this->carrera->getCarrera($est);

        $sql = "WITH tbl_clases_mat AS (
            SELECT DISTINCT mt.seccion_id, mt.estudiante_id, cl.clase_id, nt.observacion_id
            FROM tbl_matricula AS mt
            INNER JOIN tbl_seccion AS sc ON mt.seccion_id = sc.seccion_id
            INNER JOIN tbl_clase AS cl ON sc.clase_id = cl.clase_id
            LEFT JOIN tbl_notas as nt ON sc.seccion_id = nt.seccion_id
            WHERE (mt.estudiante_id = ? AND sc.periodo_academico = ?)
            OR (nt.estudiante_id = ? AND nt.observacion_id = 1)
            
        )
        SELECT cl.clase_id, cl.nombre, cl.codigo, cl.UV
        FROM tbl_clase AS cl
        LEFT JOIN tbl_clases_mat AS cm
        ON cl.clase_id = cm.clase_id
        INNER JOIN tbl_clase_carrera AS cc
        ON cl.clase_id = cc.clase_id
        WHERE cm.estudiante_id IS NULL
        AND cl.departamento_id = ?
        AND cc.carrera_id = ?;
    ";

        $result = $this->clase->customQuery($sql, [$est, $this->getPeriodo(), $est, $depID, $carID[0]["carrera"]]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "clases obtenidas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Clases no obtenidas"]);
        }
    }

    
    /**
     * Crea una clase
     *
     * @version 0.1.0
     */
    public function createClases(){

        #AuthMiddleware::authMiddleware();

        $data = json_decode(file_get_contents("php://input"), true);
        
        if ($this->clase->create($data)) {
            http_response_code(200);
            echo json_encode(["message" => "clase creada", "data" => $data]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Clase no creada"]);
        }
    }

    /**
     * retorna el id de un edificio por una clase
     * 
     * @version 0.1.0
     */
    public function getEdidByClass(){

        $header = array_change_key_case(getallheaders(), CASE_LOWER);
        if (!isset($header['claseid'])) {
            http_response_code(400);
            echo json_encode(["error" => "Claseid es requerido en el header"]);
            return;
        }

        $claseID = $header['claseid']; // Accede en minúsculas


        $sql = "SELECT cl.edificio_id
        FROM tbl_clase AS cl
        WHERE cl.clase_id = ?";

        $result = $this->clase->customQuery($sql, [$claseID]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "clases obtenidas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Clases no obtenidas"]);
        }

    }

    /**
     * Retorna las clases asignada al docente
     * 
     * @version 0.1.0
     */
    public function getClasesAsigDoc(){
        $header = array_change_key_case(getallheaders(), CASE_LOWER);
        if (!isset($header['docenteid'])) {
            http_response_code(400);
            echo json_encode(["error" => "docenteid es requerido en el header"]);
            return;
        }

        $claseID = $header['docenteid'];


        $sql = "SELECT DISTINCT cl.clase_id, cl.nombre, cl.codigo, sc.periodo_academico
                FROM tbl_clase as cl
                INNER JOIN tbl_seccion as sc
                ON cl.clase_id = sc.clase_id
                WHERE sc.docente_id = ?";

        $result = $this->clase->customQuery($sql, [$claseID]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "clases obtenidas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Clases no obtenidas"]);
        }
    }
 
    /**
     * Funcion para obtener el periodo acadmico actual
     *
     * @return "anio-trimestre" ejemplo: "2021-1"
     * 
     * @version 0.1.1
     */
    private function getPeriodo() {
        $year = date("Y");
        $mon = date("n");
    
        if ($mon >= 1 && $mon <= 4) {
            $trimestre = "I";
        } elseif ($mon >= 5 && $mon <= 8) {
            $trimestre = "II";
        } else {
            $trimestre = "III";
        }
    
        return "$year-$trimestre";
    }


}

?>
