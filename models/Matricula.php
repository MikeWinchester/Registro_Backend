<?php
require_once __DIR__ . "/BaseModel.php";

class Matricula extends BaseModel {


    public function __construct() {
        parent::__construct("tbl_matricula", "matricula_id");
    }

    public function obtenerEstudianteBySeccion($seccionid){
        $sql = "SELECT est.estudiante_id, usr.nombre_completo, usr.numero_cuenta, est.correo
        FROM tbl_matricula as mat
        left join tbl_estudiante as est
        on mat.estudiante_id = est.estudiante_id
        left join tbl_usuario as usr
        on est.usuario_id = usr.usuario_id
        where seccion_id = ?";

        return $this->fetchAll($sql, [$seccionid]);
    }

    public function obtenerEstudiantesByNotas($seccionid){
        $sql = "SELECT est.estudiante_id, usr.nombre_completo, usr.numero_cuenta, est.correo
        FROM tbl_matricula as mat
        left join tbl_estudiante as est
        on mat.estudiante_id = est.estudiante_id
        left join tbl_usuario as usr
        on est.usuario_id = usr.usuario_id
        LEFT JOIN tbl_notas as nt
        on mat.estudiante_id = nt.estudiante_id
        where mat.seccion_id = ?
        AND nt.estudiante_id is null";

        return $this->fetchAll($sql, $seccionid);
    }

    public function revisarMatricula($data){
        return $this->fetchOne(
            "SELECT COUNT(1) AS existe
             FROM tbl_matricula AS mt
             INNER JOIN tbl_lista_espera AS ep ON mt.seccion_id = ep.seccion_id
             INNER JOIN tbl_seccion as sc ON mt.seccion_id = sc.seccion_id
             WHERE (mt.estudiante_id = ? AND sc.clase_id = ?)
             OR ep.estudiante_id = ?",
            [$data['estudiante_id'], $data['clase_id'], $data['estudiante_id']]);
    }

    public function cumpleHorario($data){
        $sql = "WITH tbl_horario AS (
            SELECT horario, dias
            FROM tbl_seccion 
            WHERE seccion_id = ?
        )
        SELECT COUNT(1) AS existe
        FROM tbl_matricula AS mat
        INNER JOIN tbl_seccion AS sec
            ON mat.seccion_id = sec.seccion_id
        INNER JOIN tbl_horario AS hr
            ON sec.horario = hr.horario 
            AND sec.dias LIKE CONCAT('%', hr.dias, '%') 
        WHERE mat.estudiante_id = ?
            AND sec.periodo_academico = ?";

        return $this->fetchOne($sql, $data);
    }

    public function cupos_ocupados($seccionid){
        $sql = "SELECT count(1) as estudiantes FROM tbl_matricula WHERE seccion_id = ?";

        return $this->fetchOne($sql, $seccionid);
    }

    public function cupo_seccion($seccionid){
        $sql = "SELECT cupo_maximo FROM tbl_seccion WHERE seccion_id = ?";

        return $this->fetchOne($sql, $seccionid);
    }

    public function comprobarRequisitos($param){
        $sql = "WITH tbl_apr AS (
            SELECT sc.clase_id
            FROM tbl_notas AS nt
            INNER JOIN tbl_seccion AS sc
            ON nt.seccion_id = sc.seccion_id
            WHERE nt.estudiante_id = ?
            AND nt.observacion_id = 1
        ),
        requisitos AS (
            SELECT count(1) AS requisitos
            FROM tbl_clase_requisito AS cr
            WHERE cr.clase_id = ?
        )
        SELECT
            CASE
                WHEN r.requisitos = 0 THEN 1
                ELSE (
                    SELECT count(1)
                    FROM tbl_clase_requisito AS cr
                    INNER JOIN tbl_apr AS ap
                    ON cr.requisito_clase_id = ap.clase_id
                    WHERE cr.clase_id = ?
                )
            END AS cumple
        FROM requisitos r;
        ";

        return $this->fetchAll($sql, $param);
    }

    public function obtenerEstudiantesMatriculados($param){
        $sql = "SELECT sc.seccion_id, cl.codigo , cl.nombre, al.aula, ed.edificio , cl.UV ,sc.horario, sc.dias, sc.periodo_academico
        FROM tbl_matricula as mt
        INNER JOIN tbl_seccion as sc
        ON mt.seccion_id = sc.seccion_id
        INNER JOIN tbl_aula as al
        ON sc.aula_id = al.aula_id
        INNER JOIN tbl_clase as cl
        ON sc.clase_id = cl.clase_id
        INNER JOIN tbl_edificio as ed
        ON cl.edificio_id = ed.edificio_id
        WHERE mt.estudiante_id = ?
        AND sc.periodo_academico = ?";

        return $this->fetchAll($sql, $param);
    }

    public function obtenerHistorialMatricula($param){
        $sql = "SELECT cl.nombre, nt.calificacion, cl.codigo, cl.UV, mt.fechaInscripcion
        FROM tbl_matricula as mt
        INNER JOIN tbl_seccion as sc
        ON mt.seccion_id = sc.seccion_id
        INNER JOIN tbl_clase as cl
        ON sc.clase_id = cl.clase_id
        INNER JOIN tbl_notas as nt
        ON sc.seccion_id = nt.seccion_id
        WHERE mt.estudiante_id = ?
        ";

        return $this->fetchAll($sql, $param);
    }

    public function eliminarMatricula($param){
        $sql = "DELETE FROM tbl_matricula
        WHERE estudiante_id = ?
        AND seccion_id = ?";

        return $this->executeWrite($sql, $param);
    }

    public function actualizarSeccion($param){
        $sql = "UPDATE tbl_seccion SET cupo_maximo = cupo_maximo + 1 WHERE seccion_id = ?";

        return $this->executeWrite($sql, $param);
    }

    public function obtenerFechaMatricula(){
        $sql = "SELECT * FROM tbl_info_matricula WHERE estado_matricula_id = 1 LIMIT 1";

        return $this->fetchAll($sql);
    }

    public function obtenerIndiceGlobal($param){
        $sql = "SELECT sum(nt.calificacion * UV)/sum(UV) AS promedio
        FROM tbl_notas AS nt
        INNER JOIN tbl_seccion AS sc ON nt.seccion_id = sc.seccion_id
        INNER JOIN tbl_clase AS cl ON sc.clase_id = cl.clase_id
        WHERE nt.estudiante_id = ?";

        return $this->fetchOne($sql, $param);
    }
}

?>
