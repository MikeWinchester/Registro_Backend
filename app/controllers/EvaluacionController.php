<?php

require_once __DIR__ . "/../models/Evaluacion.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";


class EvaluacionController {
    private $evaluacion;

    public function __construct() {
        $this->evaluacion = new Evaluacion();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    public function getEvaluaciones(){
        $header = getallheaders();
    
        if (!$header) {
            http_response_code(400);
            echo json_encode(["error" => "Datos JSON inválidos"]);
            return;
        }
    
        
        if (
            (!isset($header["docenteid"]) || trim($header["docenteid"]) === "") &&
            (!isset($header["claseid"]) || trim($header["claseid"]) === "") &&
            (!isset($header["periodoacademico"]) || trim($header["periodoacademico"]) === "")
        ) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos requeridos"]);
            return;
        }
    
        $sql = "SELECT
                    usr.nombre_completo AS estudiante,
                    udc.nombre_completo AS docente,
                    usr.numero_cuenta,
                    calificacion,
                    periodo_academico,
                    comentario,
                    cl.nombre AS clase
                FROM tbl_evaluacion AS ev
                INNER JOIN tbl_seccion AS sc ON ev.seccion_id = sc.seccion_id
                INNER JOIN tbl_estudiante AS et ON ev.estudiante_id = et.estudiante_id
                INNER JOIN tbl_usuario AS usr ON et.usuario_id = usr.usuario_id
                INNER JOIN tbl_docente AS dc ON sc.docente_id = dc.docente_id
                INNER JOIN tbl_usuario AS udc ON udc.usuario_id = dc.usuario_id
                INNER JOIN tbl_clase AS cl ON sc.clase_id = cl.clase_id
                WHERE ";
    
        $conditions = [];
        $params = [];
    
        if (isset($header['docenteid']) && trim($header['docenteid']) !== "") {
            $conditions[] = 'sc.docente_id = ?';
            $params[] = $header['docenteid'];
        }
    
        if (isset($header['claseid']) && trim($header['claseid']) !== "") {
            $conditions[] = 'sc.clase_id = ?';
            $params[] = $header['claseid'];
        }
    
        if (isset($header['periodoacademico']) && trim($header['periodoacademico']) !== "") {
            $conditions[] = 'sc.periodo_academico = ?';
            $params[] = $header['periodoacademico'];
        }
    
        $sql .= implode(' AND ', $conditions);
    
        $result = $this->evaluacion->customQuery($sql, $params);
    
        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Evaluaciones encontradas correctamente", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se encontraron evaluaciones"]);
        }
    }

}
?>
