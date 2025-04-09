<?php
require_once __DIR__ . "/BaseModel.php";

class Espera extends BaseModel {


    public function __construct() {
        parent::__construct("tbl_lista_espera", "lista_espera_id");
    }

    public function obtenerListaEsperaByEstu($estudianteid){
        $sql = 'SELECT ep.seccion_id, cl.nombre ,periodo_academico, aula, horario, cupo_maximo, ed.edificio, cl.codigo, sec.dias
        FROM tbl_lista_espera as ep
        INNER JOIN tbl_seccion as sec
        ON ep.seccion_id = sec.seccion_id
        INNER JOIN tbl_clase as cl
        ON sec.clase_id = cl.clase_id
        INNER JOIN tbl_aula as al
        ON sec.aula_id = al.aula_id
        INNER JOIN tbl_edificio as ed
        ON cl.edificio_id = ed.edificio_id
        WHERE estudiante_id = ?';

        return $this->fetchAll($sql, [$estudianteid]);
    }

    public function obtenerCuposEspera($seccionid){
        $sql = 'SELECT count(1) as en_espera
        FROM tbl_lista_espera
        WHERE seccion_id = ?';

        return $this->fetchOne($sql, [$seccionid]);
    }

    public function eliminarEspera($seccionid, $estudianteid){
        $sql = 'DELETE FROM tbl_lista_espera
                WHERE seccion_id = ?
                AND estudiante_id = ?';

        return $this->executeWrite($sql, [$seccionid, $estudianteid]);
    }

    public function obtenerEsperaByDep($departamentoid){
        $sql = 'SELECT DISTINCT cl.codigo, cl.nombre, sc.horario, al.aula, ed.edificio, sc.periodo_academico, sc.seccion_id, count(1) AS solicitudes
                FROM tbl_lista_espera as lep
                INNER JOIN tbl_seccion as sc
                ON lep.seccion_id = sc.seccion_id
                INNER JOIN tbl_clase as cl
                ON sc.clase_id = cl.clase_id
                INNER JOIN tbl_aula as al
                ON sc.aula_id = al.aula_id
                INNER JOIN tbl_edificio as ed
                ON al.edificio_id = ed.edificio_id
                INNER JOIN tbl_estudiante as est
                ON lep.estudiante_id = est.estudiante_id
                INNER JOIN tbl_usuario as us
                ON est.usuario_id = us.usuario_id
                WHERE cl.departamento_id = ?
                GROUP BY cl.codigo, cl.nombre, sc.horario, al.aula, ed.edificio, sc.periodo_academico, sc.seccion_id';

        return $this->fetchAll($sql, [$departamentoid]);
    }
}
?>