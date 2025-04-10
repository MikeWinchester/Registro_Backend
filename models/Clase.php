<?php
require_once __DIR__ . "/BaseModel.php";

class Clase extends BaseModel {

    public function __construct() {
        parent::__construct("tbl_clase", "clase_id");
    }

    public function obtenerClasesPorDep($depid){
        $sql = "SELECT cl.clase_id, cl.nombre, cl.codigo, cl.UV
        FROM tbl_clase AS cl
        WHERE cl.departamento_id = ?";

        return $this->fetchAll($sql, [$depid]);
    }

    public function obtenerClasesPendientes($param){
        $sql = "WITH tbl_clases_mat AS (
                    SELECT DISTINCT mt.seccion_id, mt.estudiante_id, cl.clase_id, nt.observacion_id
                    FROM tbl_matricula AS mt
                    INNER JOIN tbl_seccion AS sc ON mt.seccion_id = sc.seccion_id
                    INNER JOIN tbl_clase AS cl ON sc.clase_id = cl.clase_id
                    LEFT JOIN tbl_notas AS nt ON sc.seccion_id = nt.seccion_id
                    WHERE (mt.estudiante_id = ? AND sc.periodo_academico = ?)
                    OR (nt.estudiante_id = ? AND nt.observacion_id = 1)

                    UNION

                    SELECT DISTINCT le.seccion_id, le.estudiante_id, cl.clase_id, NULL AS observacion_id
                    FROM tbl_lista_espera AS le
                    INNER JOIN tbl_seccion AS sc ON le.seccion_id = sc.seccion_id
                    INNER JOIN tbl_clase AS cl ON sc.clase_id = cl.clase_id
                    WHERE le.estudiante_id = ? AND sc.periodo_academico = ?
                )

                SELECT cl.clase_id, cl.nombre, cl.codigo, cl.UV
                FROM tbl_clase AS cl
                LEFT JOIN tbl_clases_mat AS cm ON cl.clase_id = cm.clase_id
                INNER JOIN tbl_clase_carrera AS cc ON cl.clase_id = cc.clase_id
                WHERE cm.estudiante_id IS NULL
                AND cl.departamento_id = ?
                AND cc.carrera_id = ?";

        return $this->fetchAll($sql, $param);
    }

    public function obtenerEdificioPorClase($claseid){
        $sql = "SELECT cl.edificio_id
        FROM tbl_clase AS cl
        WHERE cl.clase_id = ?";

        return $this->fetchAll($sql, [$claseid]);
    }

    public function obtenerClasesAsignadasDoc($docenteid, $periodo){
        $sql = "SELECT DISTINCT cl.clase_id, cl.nombre, cl.codigo, sc.periodo_academico
        FROM tbl_clase as cl
        INNER JOIN tbl_seccion as sc
        ON cl.clase_id = sc.clase_id
        WHERE sc.docente_id = ?
        AND sc.periodo_academico = ?";

        return $this->fetchAll($sql, [$docenteid, $periodo]);
    }
}
?>