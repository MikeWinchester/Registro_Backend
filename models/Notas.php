<?php
require_once __DIR__ . "/BaseModel.php";

class Notas extends BaseModel {


    public function __construct() {
        parent::__construct("tbl_notas", 'notas_id');
    }

    public function buscarNotas($header){
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

    return $this->fetchAll($sql, $params);
    }
}
?>
