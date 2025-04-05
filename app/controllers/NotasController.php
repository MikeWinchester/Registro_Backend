<?php

require_once __DIR__ . "/../models/Notas.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";

class NotasController{

    private $notas;

    public function __construct()
    {
        $this->notas = new Notas();
    }

    /**
     * Asigna notas al estudiantes
     *
     * @version 0.1.1
     */
    public function asigNotas() {
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "Datos JSON inválidos"]);
            return;
        }

        foreach ($data as $key => $estudiante) {
            if (!isset($estudiante["estudiante_id"]) || !isset($estudiante["seccion_id"]) || !isset($estudiante["nota"]) || !isset($estudiante["observacion_id"])) {
                http_response_code(400);
                echo json_encode(["error" => "Faltan datos requeridos en $key"]);
                return;
            }
    
            
            $notas = [
                "estudiante_id" => $estudiante["estudiante_id"],
                "seccion_id"    => $estudiante["seccion_id"],
                "calificacion" => $estudiante["nota"],
                "observacion_id" => $estudiante['observacion_id']
            ];

            $result = $this->notas->create($notas);

            if ($result) {
                http_response_code(200);
                echo json_encode(["message" => "Notas asignadas correctamente", "data" => $result]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "No se pudieron asignar las notas"]);
            }
        }
    
        
    }

    public function searchNotas(){
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
                    observacion,
                    cl.nombre AS clase
                FROM tbl_notas AS nt
                INNER JOIN tbl_seccion AS sc ON nt.seccion_id = sc.seccion_id
                INNER JOIN tbl_estudiante AS et ON nt.estudiante_id = et.estudiante_id
                INNER JOIN tbl_usuario AS usr ON et.usuario_id = usr.usuario_id
                INNER JOIN tbl_observacion AS ob ON nt.observacion_id = ob.observacion_id
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
    
        $result = $this->notas->customQuery($sql, $params);
    
        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Notas encontradas correctamente", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se encontraron notas"]);
        }
    }
    
    

}


?>
