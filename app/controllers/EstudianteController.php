<?php
require_once __DIR__ . "/../models/Estudiante.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class EstudianteController {
    private $estudiante;
    

    public function __construct() {
        $this->estudiante = new Estudiante();
    }


    /**
     * obtiene el perfil de estudiante
     * 
     * @version 0.1.0
     */
    public function getEspEstudiante(){
        $header = getallheaders();

        if(!isset($header['estudianteid'])){
            http_response_code(400);
            echo json_encode(["error" => "Campo estudianteid necesario"]);
        }

        $sql = "SELECT seccion_id, cl.nombre ,periodo_academico, aula, horario, cupo_maximo
        FROM tbl_lista_espera as ep
        INNER JOIN tbl_seccion as sec
        ON ep.seccion_id = sec.seccion_id
        INNER JOIN tbl_clase as cl
        ON sec.clase_id = cl.clase_id
        INNER JOIN tbl_edificio as ed
        ON cl.edificio_id = ed.edificio_id
        WHERE estudiante_id = ?";

        $estudiante = $header['estudianteid'];
        

        $result = $this->estudiante->customQuery($sql, [$estudiante]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }
    }

        /**
         * obtiene el perfil de estudiante
         * 
         * @version 0.1.0
         */
    public function getEstudiante(){
        $header = getallheaders();

        if(!isset($header['estudianteid'])){
            http_response_code(400);
            echo json_encode(["error" => "Campo estudianteid necesario"]);
        }

        $sql = "SELECT nombre_completo, numero_cuenta, nombre_carrera, nombre_centro
        FROM tbl_usuario AS usr
        INNER JOIN tbl_estudiante as est
        ON usr.usuario_id = est.usuario_id
        INNER JOIN tbl_carrera as cr
        ON est.carrera_id = cr.carrera_id
        INNER JOIN tbl_centro_regional as tcr
        ON tcr.centro_regional_id = est.centro_regional_id
        WHERE estudiante_id = ?";

        $estudiante = $header['estudianteid'];
        

        $result = $this->estudiante->customQuery($sql, [$estudiante]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }
    }

    public function getEstudianteByCuenta(){
        $header = getallheaders();

        if(!isset($header['cuenta'])){
            http_response_code(400);
            echo json_encode(["error" => "Campo cuenta necesario"]);
        }

        $sql = "SELECT nombre_completo, numero_cuenta, nombre_carrera, nombre_centro
        FROM tbl_usuario AS usr
        INNER JOIN tbl_estudiante as est
        ON usr.usuario_id = est.usuario_id
        INNER JOIN tbl_carrera as cr
        ON est.carrera_id = cr.carrera_id
        INNER JOIN tbl_centro_regional as tcr
        ON tcr.centro_regional_id = est.centro_regional_id
        WHERE usr.numero_cuenta = ?";

        $estudiante = $header['cuenta'];
        

        $result = $this->estudiante->customQuery($sql, [$estudiante]);

        $result[0]['indice_global'] = $this->getIndiceGlobal($estudiante);
        $result[0]['indice_periodo'] = $this->getIndicePeriodo($estudiante);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Estudiante encontrado", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Estudiante  no encontrado"]);
        }
    }

    private function getIndiceGlobal($numero_cuenta){
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

        $result = $this->estudiante->customQuery($sql, [$numero_cuenta]);

        if($result){
            return $result[0]['indice_global'];
        }

        return 0;
    }

    private function getIndicePeriodo($numero_cuenta){
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

        $result = $this->estudiante->customQuery($sql, [$numero_cuenta, $this->getPeriodo()]);

        if($result[0]['indice_periodo'] != null){
            return $result[0]['indice_periodo'];
        }

        $result = $this->estudiante->customQuery($sql, [$numero_cuenta, $this->getPeriodoPasado()]);

        if($result[0]['indice_periodo'] != null){
            return $result[0]['indice_periodo'];
        }

        return 0;
    }


    public function getHistorial(){
        $header = getallheaders();

        if(!isset($header['cuenta'])){
            http_response_code(400);
            echo json_encode(["error" => "Campo cuenta necesario"]);
        }

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

        $estudiante = $header['cuenta'];
        
        $result = $this->estudiante->customQuery($sql, [$estudiante]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Historial encontrado", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Historial no encontrado"]);
        }
    }

    public function getUsuarioByEstu(){
        $header = getallheaders();

        $sql = "SELECT usuario_id 
                FROM tbl_estudiante
                WHERE estudiante_id = ?";

        $result = $this->estudiante->customQuery($sql, $header['estudianteid']);

        if($result){
            http_response_code(200);
            echo json_encode(['message' => 'usuario obtenido', "data" => $result]);
        }else{
            http_response_code(400);
            echo json_encode(['error' => 'usuario no obtenido']);
        }
    }

    /**
     * Funcion para obtener el periodo acadmico actual
     *
     * @return "anio-trimestre" ejemplo: "2021-1"
     * 
     * @version 0.1.1
     */
    private function getPeriodo() {
        $year = date("Y");
        $mon = date("n");
    
        if ($mon >= 1 && $mon <= 4) {
            $trimestre = "I";
        } elseif ($mon >= 5 && $mon <= 8) {
            $trimestre = "II";
        } else {
            $trimestre = "III";
        }
    
        return "$year-$trimestre";
    }

    private function getPeriodoPasado() {
        
        $periodoActual = $this->getPeriodo();
        $anio = explode("-",$periodoActual)[0];
        $periodo = explode("-",$periodoActual)[1];

        if($periodo == 'I'){
            $anio = intval($anio) - 1;
            $periodo = 'III';
        }elseif($periodo == 'II'){
            $periodo = 'I';
        }elseif($periodo == 'III'){
            $periodo = 'II';
        }
    
        return "$anio-$periodo";
    }
}
?>