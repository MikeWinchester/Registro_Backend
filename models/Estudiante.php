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
        $sql = "SELECT nombre_completo, numero_cuenta, nombre_carrera, nombre_centro, descripcion, usr.correo, telefono, foto_perfil
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
        $sql = "SELECT usr.usuario_id ,nombre_completo, numero_cuenta, nombre_carrera, nombre_centro
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

    public function obtenerIndiceGlobalById($cuenta){
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
        WHERE ur.id = ?";

        return $this->fetchOne($sql, [$cuenta]);
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

    public function obtenerEstudianteId($param){
        
        $sql = "SELECT estudiante_id AS id
                FROM tbl_estudiante AS et
                INNER JOIN tbl_usuario AS us
                ON et.usuario_id = us.usuario_id
                WHERE us.id = ?";

        return $this->fetchOne($sql, $param);
    
    }
    

    public function obtenerHistorialEstudiante($busqueda, $carrera){
        
        $sql = "SELECT tbl_usuario.nombre_completo, tbl_carrera.nombre_carrera, tbl_centro_regional.nombre_centro, 
                       tbl_usuario.correo, AVG(tbl_notas.calificacion) as Promedio 
                FROM tbl_estudiante  
                INNER JOIN tbl_centro_regional ON tbl_centro_regional.centro_regional_id = tbl_estudiante.centro_regional_id 
                INNER JOIN tbl_usuario ON tbl_estudiante.usuario_id = tbl_usuario.usuario_id 
                INNER JOIN tbl_carrera ON tbl_estudiante.carrera_id = tbl_carrera.carrera_id
                INNER JOIN tbl_notas ON tbl_notas.estudiante_id = tbl_estudiante.estudiante_id";
    
        // Filtros dinámicos
        $condiciones = [];
    
        if (!empty($busqueda)) {
            $busqueda = addslashes($busqueda); // Evitar inyección SQL
            $condiciones[] = "(tbl_usuario.nombre_completo LIKE '%$busqueda%' OR tbl_usuario.numero_cuenta LIKE '%$busqueda%')";
        }
    
        if (!empty($carrera)) {
            $carrera = addslashes($carrera);
            $condiciones[] = "tbl_carrera.nombre_carrera = '$carrera'";
        }
    
        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
    
        $sql .= " GROUP BY tbl_estudiante.estudiante_id";

        return $this->fetchAll($sql);
    }

    public function actualizarDescripcion($param){
        $sql = 'UPDATE tbl_estudiante
                SET descripcion = ?
                WHERE usuario_id = ?';

        return $this->executeWrite($sql, [$param['descripcion'] , $param['usuario_id']]);
    }

    public function uploadData($param){
        $sql = "UPDATE tbl_estudiante AS et
                JOIN tbl_usuario AS us ON et.usuario_id = us.usuario_id
                SET et.foto_perfil = ?
                WHERE us.id = ?";

        
        return $this->executeWrite($sql, $param);
    }

    public function validateGaleria($param){
        $sql = "SELECT count(1) AS cantidad_fotos
                FROM tbl_recurso_estudiante as re
                INNER JOIN tbl_estudiante as et
                ON et.estudiante_id = re.estudiante_id
                INNER JOIN tbl_usuario as us
                ON us.usuario_id = et.usuario_id
                WHERE us.id = ?";

        return $this->fetchOne($sql, $param);
    }

    public function uploadGaleria($param){
        $param[0] = $this->getEstId([$param[0]]);

        $sql = "INSERT INTO tbl_recurso_estudiante(estudiante_id, fotografia)
                VALUES (?,?)";

        
        return $this->executeWrite($sql, $param);
    }

    private function getEstId($param){
        $sql = "SELECT et.estudiante_id AS id
                FROM tbl_estudiante as et
                INNER JOIN tbl_usuario as us
                ON et.usuario_id = us.usuario_id
                WHERE us.id = ?";

        return $this->fetchOne($sql, $param)['id'];
    }

    public function getRouteGaleria($param){
        $sql = "SELECT fotografia
                FROM tbl_recurso_estudiante AS re
                INNER JOIN tbl_estudiante AS et
                ON et.estudiante_id = re.estudiante_id
                INNER JOIN tbl_usuario AS us
                ON et.usuario_id = us.usuario_id
                WHERE us.id = ?";

        return $this->fetchAll($sql, $param);
    }

    public function eliminarFoto($param){
        $sql = "DELETE FROM tbl_recurso_estudiante WHERE fotografia = ?";

        return $this->executeWrite($sql, $param);
    }

}
?>
