<?php
require_once __DIR__ . "/BaseModel.php";

class Seccion extends BaseModel {


    public function __construct() {
        parent::__construct("tbl_seccion", "seccion_id");
    }

    public function obtenerSeccionesActuByDoc($param){
        $sql = "SELECT seccion_id, cl.nombre ,periodo_academico, al.aula, horario, cupo_maximo, cl.codigo
        FROM tbl_seccion as sec
        INNER JOIN tbl_clase as cl
        ON sec.clase_id = cl.clase_id
        INNER JOIN tbl_aula as al
        ON sec.aula_id = al.aula_id
        WHERE docente_id = ?
        AND periodo_academico = ?
        ";

        return $this->fetchAll($sql, $param);
    }

    public function obtenerSeccionesByDoc($param){
        $sql = "SELECT seccion_id, cl.nombre ,periodo_academico, aula, horario, cupo_maximo
        FROM tbl_seccion as sec
        INNER JOIN tbl_clase as cl
        ON sec.clase_id = cl.clase_id
        WHERE docente_id = ?";

        return $this->fetchAll($sql, $param);
    }

    public function obtenerSeccion($param){
        $sql = "SELECT cl.nombre, sec.periodo_academico, al.aula, sec.horario, sec.cupo_maximo, usr.nombre_completo, usr.correo
        FROM tbl_seccion as sec
        INNER JOIN tbl_docente as doc
        ON sec.docente_id = doc.docente_id
        INNER JOIN tbl_usuario as usr
        ON doc.usuario_id = usr.usuario_id
        INNER JOIN tbl_clase as cl
        ON sec.clase_id = cl.clase_id
        INNER JOIN tbl_aula as al
        ON sec.aula_id = al.aula_id
        WHERE sec.seccion_id = ?
        ";

        return $this->fetchAll($sql, $param);
    }

    public function obtenerCantidadSeccion($param){
        $sql = "SELECT count(1) as cantidad
        FROM tbl_seccion
        WHERE docente_id = ?
        AND periodo_academico = ?
        ";

        return $this->fetchAll($sql, $param);
    }

    public function obtenerCentroByJefe($param){
        $sql = 'SELECT centro_regional_id AS id
                FROM tbl_jefe AS jf
                INNER JOIN tbl_docente AS dc
                ON jf.docente_id = dc.docente_id
                WHERE jefe_id = ?';

        return $this->fetchOne($sql,$param);
    }

    public function validarSeccion($param){
        $sql = "SELECT COUNT(1) AS existe
        FROM tbl_seccion
        WHERE docente_id = ?
        AND horario = ?
        AND periodo_academico = ?
        AND (
            dias LIKE CONCAT('%', ?, '%')
        )
        ";

        return $this->fetchOne($sql, $param);
    }

    public function obtenerSeccionesByEstu($param){
        $sql = "SELECT sc.seccion_id, us.nombre_completo, sc.horario, al.aula, sc.cupo_maximo
        FROM tbl_seccion AS sc
        INNER JOIN tbl_docente AS dc
        ON sc.docente_id = dc.docente_id
        INNER JOIN tbl_usuario AS us
        on dc.usuario_id = us.usuario_id
        INNER JOIN tbl_aula as al
        ON sc.aula_id = al.aula_id
        WHERE sc.clase_id = ?
        AND sc.periodo_academico = ?
        AND sc.centro_regional_id = ?";

        return $this->fetchAll($sql, $param);
    }

    public function obtenerSeccionesByClassCentro($param){
        $sql = "SELECT sc.seccion_id, us.nombre_completo, sc.horario, al.aula, sc.cupo_maximo
        FROM tbl_seccion AS sc
        INNER JOIN tbl_docente AS dc
        ON sc.docente_id = dc.docente_id
        INNER JOIN tbl_usuario AS us
        on dc.usuario_id = us.usuario_id
        INNER JOIN tbl_aula as al
        ON sc.aula_id = al.aula_id
        WHERE sc.clase_id = ?
        AND sc.periodo_academico = ?
        AND sc.centro_regional_id = ?";

        return $this->fetchAll($sql, $param);
    }

    public function getCentroByJefe($param){
        $sql = "SELECT centro_regional_id AS id
                FROM tbl_jefe AS jf
                INNER JOIN tbl_docente AS dc
                ON jf.docente_id = dc.docente_id
                WHERE jf.jefe_id = ? ";

        return $this->fetchOne($sql, [$param]);
    }

    public function getCentroByEstu($param){
        $sql = 'SELECT centro_regional_id AS id
                FROM tbl_estudiante as et
                WHERE estudiante_id = ?';

        return $this->fetchOne($sql,$param);
    }

    public function obtenerSeccionClaseByDoc($param){
        $sql = "SELECT sc.seccion_id, us.nombre_completo, sc.horario, al.aula, sc.cupo_maximo
        FROM tbl_seccion AS sc
        INNER JOIN tbl_docente AS dc
        ON sc.docente_id = dc.docente_id
        INNER JOIN tbl_usuario AS us
        on dc.usuario_id = us.usuario_id
        INNER JOIN tbl_aula as al
        ON sc.aula_id = al.aula_id
        WHERE sc.clase_id = ?
        AND sc.docente_id = ?
        AND sc.periodo_academico = ?";

        return $this->fetchAll($sql, $param);
    }

    public function obtenerCupoOcupado($param){
        $sql = "SELECT count(1) as estudiantes FROM tbl_matricula WHERE seccion_id = ?";

        return $this->fetchOne($sql, $param);
    }

    public function obtenerCupoSeccion($param){
        $sql = "SELECT cupo_maximo FROM tbl_seccion WHERE seccion_id = ?";

        return $this->fetchOne($sql, $param);
    }

    public function horarios($diasArray, $param){
        $sql = "SELECT DISTINCT horario FROM tbl_seccion WHERE (docente_id = ? AND periodo_academico = ?) OR aula_id = ?";
    
        if (count($diasArray) > 0) {
            $sql .= " AND (";
    
            $conditions = [];
            foreach ($diasArray as $dia) {
                $conditions[] = "dias LIKE ?";
                $param[] = "%$dia%";
            }
    
            $sql .= implode(" OR ", $conditions) . ")";
        }


        return $this->fetchAll($sql, $param);
    }

    public function obtenerPeriodoAcademico(){
        $sql = "SELECT DISTINCT periodo_academico
        FROM tbl_seccion";

        return $this->fetchAll($sql);
    }

    public function actualizarDocente($param){
        $sql = 'UPDATE tbl_seccion SET docente_id = ? WHERE seccion_id = ?';

        return $this->executeWrite($sql, $param);
    }

    public function actualizarCupos($param){
        $sql = 'UPDATE tbl_seccion SET cupo_maximo = ? WHERE seccion_id = ?';

        return $this->executeWrite($sql, $param);
    }

    public function actualizarDocAndCupo($param){
        $sql = 'UPDATE tbl_seccion SET cupo_maximo = ?, docente_id = ? WHERE seccion_id = ?';

        return $this->executeWrite($sql, $param);
    }

    public function obtenerCuposMaximos($param){
        $sql = 'SELECT cupo_maximo
                FROM tbl_seccion
                WHERE seccion_id = ?';

        return $this->fetchOne($sql, $param);
    }

    public function obtenerEstudiantesEspera($param){
        $sql = "SELECT lep.estudiante_id AS id
                FROM tbl_lista_espera AS lep
                WHERE seccion_id = ?
                ORDER BY (lista_espera_id)";

        return $this->fetchAll($sql, $param);
    }

    public function eliminarEstudianteEspera($param){
        $sql = "DELETE FROM tbl_lista_espera WHERE estudiante_id = ?";

        return $this->executeWrite($sql, $param);
    }

    public function matricularEstudianteEspera($param){
        $sql = "SELECT 1 FROM tbl_matricula WHERE estudiante_id = ? AND seccion_id = ?";

        return $this->fetchAll($sql, $param);
    }

    public function eliminarMatricula($param){
        $sqlEst = 'DELETE FROM tbl_matricula WHERE seccion_id = ?';

        return $this->executeWrite($sqlEst, $param);
    }

    public function eliminarEspera($param){
        $sqlEsp = 'DELETE FROM tbl_lista_espera WHERE seccion_id = ?';

        return $this->executeWrite($sqlEsp, $param);
    }

    public function eliminarSeccion($param){
        $sqlSec = 'DELETE FROM tbl_seccion WHERE seccion_id = ?';

        return $this->executeWrite($sqlSec, $param);
    }
}
?>
