<?php
require_once __DIR__ . "/BaseModel.php";

class Evaluacion extends BaseModel {


    public function __construct() {
        parent::__construct("tbl_evaluacion", "docente_id");
    }

    public function obtenerEvaluaciones($header){
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
    }
}

?>