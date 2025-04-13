<?php
require_once __DIR__ . "/BaseModel.php";

class Docente extends BaseModel {

    public function __construct() {
        parent::__construct("tbl_docente", "docente_id");
    }

    public function crearDocente($data){
        $sql ="INSERT INTO tbl_usuario (nombre_completo, identidad, correo, contrasenia, numero_cuenta, telefono)
                VALUES (?, ?, ?, ?, ?, ?)";

        return $this->executeWrite($sql, $data);
    }

    public function obtenerUsuarioID($correo){
        $sql = "SELECT usuario_id FROM tbl_usuario WHERE correo=?";

        return $this->fetchOne($sql, [$correo]);
    }

    public function obtenerPerfilDocente($docenteid){
        $sql = "SELECT usr.nombre_completo, usr.correo, usr.numero_cuenta, cr.nombre_centro, crr.nombre_carrera
        FROM tbl_docente AS doc
        INNER JOIN tbl_usuario AS usr
        ON doc.usuario_id = usr.usuario_id
        INNER JOIN tbl_centro_regional AS cr
        ON doc.centro_regional_id = cr.centro_regional_id
        INNER JOIN tbl_carrera as crr
        on doc.carrera_id = crr.carrera_id
        WHERE docente_id = ?";

        return $this->fetchOne($sql, [$docenteid]);
    }

    public function obtenerTodosDocentes(){
        $sql = "SELECT usr.nombre_completo, usr.correo, usr.numero_cuenta, cr.nombre_centro, crr.nombre_carrera
        FROM tbl_docente AS doc
        INNER JOIN tbl_usuario AS usr
        ON doc.usuario_id = usr.usuario_id
        INNER JOIN tbl_centro_regional AS cr
        ON doc.centro_regional_id = cr.centro_regional_id
        INNER JOIN tbl_carrera as crr
        on doc.carrera_id = crr.carrera_id";

        return $this->fetchAll($sql);
    }

    public function listaDocentes($docenteid, $centroid){
        $sql = "SELECT doc.docente_id, usr.nombre_completo
        FROM tbl_docente AS doc
        INNER JOIN tbl_usuario AS usr
        ON doc.usuario_id = usr.usuario_id
        WHERE doc.departamento_id = ?
        AND doc.centro_regional_id = ?";

        return $this->fetchAll($sql, [$docenteid, $centroid]);
    }

    public function listaDocentesDispo($diasCondiciones,$param){
        $sql = "
        WITH docentes_ocupados AS (
            SELECT sc.docente_id
            FROM tbl_seccion AS sc
            WHERE sc.periodo_academico = ?
            AND (
                TIME(SUBSTRING_INDEX(sc.horario, '-', 1)) < TIME(?) 
                AND TIME(SUBSTRING_INDEX(sc.horario, '-', -1)) > TIME(?)
            )
            AND ($diasCondiciones)
        )
        SELECT dc.docente_id, usr.nombre_completo
        FROM tbl_docente AS dc
        INNER JOIN tbl_usuario AS usr ON dc.usuario_id = usr.usuario_id
        LEFT JOIN docentes_ocupados AS dcc ON dc.docente_id = dcc.docente_id
        WHERE dc.departamento_id = ?
        AND dc.centro_regional_id = ?
        AND dcc.docente_id IS NULL
        ";

        return $this->fetchAll($sql, $param);
    }

    public function obtenerHorarioBySeccion($seccionid){
        $sql = 'SELECT horario, dias FROM tbl_seccion WHERE seccion_id = ?';
        return $this->fetchAll($sql, [$seccionid]);
    }

    public function obtenerUsuarioByDoc($docenteid){
        $sql = "SELECT usuario_id 
        FROM tbl_docente
        WHERE docente_id = ?";

        return $this->fetchOne($sql, [$docenteid]);
    }

    public function obtenerDocenteId($param){
        $sql = "SELECT docente_id AS id
                FROM tbl_docente AS dc
                INNER JOIN tbl_usuario AS us
                ON dc.usuario_id = us.usuario_id
                WHERE us.id = ?";

        return $this->fetchOne($sql, $param);
    }

    public function uploadData($param){
        $sql = "UPDATE tbl_docente AS dc
                INNER JOIN tbl_usuario AS us ON dc.usuario_id = us.usuario_id 
                SET ";
        $conditions = [];
        $values = [];
    
        if (!empty($param['foto_perfil'])) {
            $conditions[] = "foto_perfil = ?";
            $values[] = $param['foto_perfil'];
        }
    
        if (!empty($param['descripcion'])) {
            $conditions[] = "descripcion = ?";
            $values[] = $param['descripcion'];
        }
    
        if (empty($conditions)) {
            return false;
        }
    
        $sql .= implode(", ", $conditions);
        $sql .= " WHERE us.id = ?";
        $values[] = $param['docente_id'];
    
        return $this->executeWrite($sql, $values);
    }

    public function uploadVideoSql($param){
        $sql = 'INSERT INTO tbl_recurso(seccion_id, titulo, video, descripcion) VALUES (?,?,?,?)';

        return $this->executeWrite($sql, $param);
    }
    
}


?>
