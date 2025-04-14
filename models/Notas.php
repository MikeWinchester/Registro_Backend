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

    if (isset($header['Docenteid']) && trim($header['Docenteid']) !== "") {
    $conditions[] = 'sc.docente_id = ?';
    $params[] = $header['Docenteid'];
    }

    if (isset($header['Claseid']) && trim($header['Claseid']) !== "") {
    $conditions[] = 'sc.clase_id = ?';
    $params[] = $header['Claseid'];
    }

    if (isset($header['Periodoacademico']) && trim($header['Periodoacademico']) !== "") {
    $conditions[] = 'sc.periodo_academico = ?';
    $params[] = $header['Periodoacademico'];
    }

    $sql .= implode(' AND ', $conditions);

    return $this->fetchAll($sql, $params);
    }

    public function obtenerFechaNotas(){
        $sql = "SELECT inicio, final FROM tbl_info_notas WHERE estado_matricula_id = 1 LIMIT 1";

        return $this->fetchOne($sql);
    }

    public function obtenerNotasEstu($params){
        $sql = "SELECT 
                cl.nombre AS clase,
                us.nombre_completo AS docente,
                dc.docente_id,
                sc.seccion_id,
                CASE 
                    WHEN ev.estudiante_id IS NOT NULL THEN nt.calificacion
                    ELSE NULL
                END AS calificacion
            FROM tbl_matricula AS mt
            INNER JOIN tbl_seccion AS sc ON mt.seccion_id = sc.seccion_id
            INNER JOIN tbl_clase AS cl ON sc.clase_id = cl.clase_id
            INNER JOIN tbl_docente AS dc ON sc.docente_id = dc.docente_id
            INNER JOIN tbl_usuario AS us ON dc.usuario_id = us.usuario_id
            LEFT JOIN tbl_evaluacion AS ev 
                ON ev.seccion_id = sc.seccion_id AND ev.estudiante_id = mt.estudiante_id
            LEFT JOIN tbl_notas AS nt 
                ON nt.seccion_id = sc.seccion_id AND nt.estudiante_id = mt.estudiante_id
            WHERE mt.estudiante_id = ?
            AND sc.periodo_academico = ?";

        return $this->fetchAll($sql, $params);
    }

    public function crearEvaluacion($param){
        $sql = "INSERT INTO tbl_evaluacion VALUES (?,?,?,?)";

        return $this->executeWrite($sql, $param);
    }

}
?>
