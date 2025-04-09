<?php

require_once __DIR__ . "/../models/Docente.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";

require_once __DIR__ . "/../controllers/JefeController.php";

class DocenteController {
    private $docente;
    private $jefe;

    public function __construct() {
        $this->docente = new Docente();
        $this->jefe = new JefeController();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    /**
     * Funcion para crear al docente y a su usuario
     *
     * @version 0.1.1
     */
    public function createDocente() {
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!isset($data["nombre_completo"]) || !isset($data["identidad"]) || !isset($data["correo"]) ||
            !isset($data["contrasenia"]) ||  !isset($data["numero_cuenta"]) ||
            !isset($data["centro_regional_id"])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos requeridos"]);
            return;
        }
    
        // Hashear la contraseña
        $data["contrasenia"] = password_hash($data["contrasenia"], PASSWORD_DEFAULT);
    
        // Separar los datos de usuario y docente
        $data_usr = [
            "nombre_completo" => $data["nombre_completo"],
            "identidad" => $data["identidad"],
            "correo" => $data["correo"],
            "contrasenia" => $data["contrasenia"],
            "numero_cuenta" => $data["numero_cuenta"],
            "telefono" => isset($data["telefono"]) ? $data["telefono"] : null,
        ];
    
        // Insertar usuario
        $this->docente->customQueryInsert(
            "INSERT INTO tbl_usuario (nombre_completo, identidad, correo, contrasenia, numero_cuenta, telefono)
             VALUES (?, ?, ?, ?, ?, ?)",
            array_values($data_usr)
        );
    
        // Obtener el UsuarioID recién insertado
        $usuario = $this->docente->customQuery(
            "SELECT usuario_id FROM tbl_usuario WHERE correo=?",
            [$data['correo']]
        );
    
        if (!$usuario || count($usuario) == 0) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener UsuarioID"]);
            return;
        }
    
        $data_doc = [
            "usuario_id" => $usuario[0]['usuario_id'],
            "centro_regional_id" => $data["centro_regional_id"],
            "carrera_id" => $data["carrera_id"],
        ];
    
        // Insertar docente
        if ($this->docente->create($data_doc)) {
            echo json_encode(["message" => "Docente creado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear Docente"]);
        }
    }

    
    /**
     * funcion para obtener al docente
     *
     * @param $idDocente id del docente seleccionado
     * @version 0.1.1
     */
    public function getDocente(){
        
        #AuthMiddleware::authMiddleware();

        $header = getallheaders();

        if(!isset($header['docenteid'])){
            echo "clabe docenteid necesria";
            return;
        }

        $docenteid = $header['docenteid'];

        $sql = "SELECT usr.nombre_completo, usr.correo, usr.numero_cuenta, cr.nombre_centro, crr.nombre_carrera
        FROM tbl_docente AS doc
        INNER JOIN tbl_usuario AS usr
        ON doc.usuario_id = usr.usuario_id
        INNER JOIN tbl_centro_regional AS cr
        ON doc.centro_regional_id = cr.centro_regional_id
        INNER JOIN tbl_carrera as crr
        on doc.carrera_id = crr.carrera_id
        WHERE docente_id = ?";

        $result = $this->docente->customQuery($sql, [$docenteid]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Docente obtenido", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Docente no obtenido"]);
        }
    }

    /**
     * Obtiene todos los docentes
     *
     * @version 0.1.2
     */
    public function getAllDocentes(){
        #AuthMiddleware::authMiddleware();

        $sql = "SELECT usr.nombre_completo, usr.correo, usr.numero_cuenta, cr.nombre_centro, crr.nombre_carrera
        FROM tbl_docente AS doc
        INNER JOIN tbl_usuario AS usr
        ON doc.usuario_id = usr.usuario_id
        INNER JOIN tbl_centro_regional AS cr
        ON doc.centro_regional_id = cr.centro_regional_id
        INNER JOIN tbl_carrera as crr
        on doc.carrera_id = crr.carrera_id";

        $result = $this->docente->customQuery($sql);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Docentes obtenidos", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Docentes no obtenidos"]);
        }
    }

    /**
     * Obtiene todos los docentes del centro regional
     *
     * @version 0.1.2
     */
    public function getDocentesBydepartment(){

        $header = getallheaders();

        $dep = $header['areaid'];
        $centro = $this->jefe->getCentroByJefe($header['jefeid']);

        $sql = "SELECT doc.docente_id, usr.nombre_completo
        FROM tbl_docente AS doc
        INNER JOIN tbl_usuario AS usr
        ON doc.usuario_id = usr.usuario_id
        WHERE doc.departamento_id = ?
        AND doc.centro_regional_id = ?";

        $result = $this->docente->customQuery($sql, [$dep, $centro[0]['id']]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Docentes obtenidos", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Docentes no obtenidos"]);
        }
    }

    public function getDocentesByHorario() {
        header('Content-Type: application/json');
        $header = getallheaders();
    
        $sec = $header['seccionid'];
        $dep = $header['areaid'];
        $centro = $this->jefe->getCentroByJefe($header['jefeid'])[0]['id'];
        $periodo = $this->getPeriodo();
    
        $horario = $this->getHorarioSeccion($sec);
    
        if (!$horario || empty($horario[0])) {
            http_response_code(400);
            echo json_encode(["error" => "No se encontró el horario de la sección."]);
            return;
        }
    
        $horarioData = $horario[0];
        list($inicio, $fin) = explode('-', $horarioData['horario']);
        $dias = explode(',', str_replace(' ', '', $horarioData['dias'])); // ["Lun", "Mar", ...]
    
        $diasCondiciones = implode(' OR ', array_fill(0, count($dias), "sc.dias LIKE ?"));
        $diasParams = array_map(fn($d) => "%$d%", $dias);
    
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
    
        $params = array_merge(
            [$periodo, $fin, $inicio],
            $diasParams,
            [$dep, $centro]
        );
    
        $result = $this->docente->customQuery($sql, $params);
    
        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Docentes disponibles obtenidos", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se encontraron docentes disponibles"]);
        }
    }
    
    private function getHorarioSeccion($sec) {
        $sql = 'SELECT horario, dias FROM tbl_seccion WHERE seccion_id = ?';
        return $this->docente->customQuery($sql, [$sec]);
    }
    
    public function getUsuarioByDocente(){
        $header = getallheaders();

        $sql = "SELECT usuario_id 
                FROM tbl_docente
                WHERE docente_id = ?";

        $result = $this->docente->customQuery($sql, $header['docenteid']);

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
}

?>
