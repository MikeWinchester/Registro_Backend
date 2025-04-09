<?php
require_once __DIR__ . "/BaseModel.php";

class Estudiante extends BaseModel {


    public function __construct() {
        parent::__construct("tbl_estudiante", "estudiante_id");
    }

    public function obtenerEsperaEstudiante($estudianteid){
        $sql = "SELECT seccion_id, cl.nombre ,periodo_academico, aula, horario, cupo_maximo
        FROM tbl_lista_espera as ep
        INNER JOIN tbl_seccion as sec
        ON ep.seccion_id = sec.seccion_id
        INNER JOIN tbl_clase as cl
        ON sec.clase_id = cl.clase_id
        INNER JOIN tbl_edificio as ed
        ON cl.edificio_id = ed.edificio_id
        WHERE estudiante_id = ?";

        return $this->fetchAll($sql, [$estudianteid]);
    }

    public function obtenerPerfilEstudiante($estudianteid){
        $sql = "SELECT nombre_completo, numero_cuenta, nombre_carrera, nombre_centro
        FROM tbl_usuario AS usr
        INNER JOIN tbl_estudiante as est
        ON usr.usuario_id = est.usuario_id
        INNER JOIN tbl_carrera as cr
        ON est.carrera_id = cr.carrera_id
        INNER JOIN tbl_centro_regional as tcr
        ON tcr.centro_regional_id = est.centro_regional_id
        WHERE estudiante_id = ?";

        return $this->fetchOne($sql, [$estudianteid]);

    }

    public function obtenerEstudianteByCuenta($cuenta){
        $sql = "SELECT nombre_completo, numero_cuenta, nombre_carrera, nombre_centro
        FROM tbl_usuario AS usr
        INNER JOIN tbl_estudiante as est
        ON usr.usuario_id = est.usuario_id
        INNER JOIN tbl_carrera as cr
        ON est.carrera_id = cr.carrera_id
        INNER JOIN tbl_centro_regional as tcr
        ON tcr.centro_regional_id = est.centro_regional_id
        WHERE usr.numero_cuenta = ?";

        return $this->fetchOne($sql, [$cuenta]);
    }

    public function obtenerIndiceGlobal($cuenta){
        $sql = "SELECT ROUND(((sum(calificacion * UV)) / sum(UV)),2) AS indice_global
        FROM tbl_notas AS nt
        INNER JOIN tbl_estudiante AS et
        ON nt.estudiante_id = et.estudiante_id
        INNER JOIN tbl_usuario AS ur
        ON et.usuario_id = ur.usuario_id
        INNER JOIN tbl_seccion AS sc
        ON nt.seccion_id = sc.seccion_id
        INNER JOIN tbl_clase AS cl
        ON sc.clase_id = cl.clase_id
        WHERE ur.numero_cuenta = ?";

        return $this->fetchOne($sql, [$cuenta]);
    }

    public function obtenerIndicePeriodo($cuenta, $periodo){
        $sql = "SELECT ROUND(((sum(calificacion * UV)) / sum(UV)),2) AS indice_periodo
        FROM tbl_notas AS nt
        INNER JOIN tbl_estudiante AS et
        ON nt.estudiante_id = et.estudiante_id
        INNER JOIN tbl_usuario AS ur
        ON et.usuario_id = ur.usuario_id
        INNER JOIN tbl_seccion AS sc
        ON nt.seccion_id = sc.seccion_id
        INNER JOIN tbl_clase AS cl
        ON sc.clase_id = cl.clase_id
        WHERE ur.numero_cuenta = ?
        AND periodo_academico = ?";

        return $this->fetchOne($sql, [$cuenta, $periodo]);
    }

    public function obtenerHistorialByCuenta($cuenta){
        $sql = "SELECT cl.codigo, cl.nombre, cl.UV, sc.horario, sc.periodo_academico, nt.calificacion, ob.observacion
        FROM tbl_notas AS nt
        INNER JOIN tbl_seccion AS sc
        ON nt.seccion_id = sc.seccion_id
        INNER JOIN tbl_clase AS cl
        ON sc.clase_id = cl.clase_id
        INNER JOIN tbl_observacion AS ob
        ON ob.observacion_id = nt.observacion_id
        INNER JOIN tbl_estudiante AS et
        ON nt.estudiante_id = et.estudiante_id
        INNER JOIN tbl_usuario AS ur
        ON et.usuario_id = ur.usuario_id
        WHERE ur.numero_cuenta = ?";

        return $this->fetchAll($sql, [$cuenta]);
    }

    public function obtenerUsuarioByEstudiante($estudianteid){
        $sql = "SELECT usuario_id 
        FROM tbl_estudiante
        WHERE estudiante_id = ?";

        return $this->fetchOne($sql, [$estudianteid]);
    }
}
?>
