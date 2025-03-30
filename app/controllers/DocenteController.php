<?php

require_once __DIR__ . "/../models/Docente.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";

class DocenteController {
    private $docente;

    public function __construct() {
        $this->docente = new Docente();
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

        if(!isset($header['areaid'])){
            http_response_code(400);
            echo json_encode(["error" => "areaid es requerido en el header"]);
            return;
        }
    
        $centroID = $header['areaid'];

        $sql = "SELECT doc.docente_id, usr.nombre_completo
        FROM tbl_docente AS doc
        INNER JOIN tbl_usuario AS usr
        ON doc.usuario_id = usr.usuario_id
        WHERE doc.departamento_id = ?";
        
        $result = $this->docente->customQuery($sql, [$centroID]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Docentes obtenidos", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Docentes no obtenidos"]);
        }
    }


    /**
     * Guardar video subido de la seccion correspondiente
     *
     * @param $idSeccion id de la seccion
     * @param $data json donde ira el archivo
     *
     * @version 0.1.1
     */
    //Problema no soporta video grandes
    public function uploadVideo() {

        if (!isset($_POST['idSeccion']) || !isset($_FILES['video'])) {
            echo json_encode(["error" => "Faltan datos (idSeccion o video)"]);
            return;
        }

        
    
        $idSeccion = $_POST['idSeccion']; 
        $video = $_FILES['video'];
        $ruta = __DIR__ . "/../uploads/Videos/$idSeccion";

        echo ini_get('upload_max_filesize') . "\n";
        echo ini_get('post_max_size') . "\n";

        echo var_export($_FILES, true);
    
        $this->checkFolder($idSeccion,$ruta);

        $maxSize = 200 * 1024 * 1024; 

        if ($video['size'] > $maxSize) {
            echo json_encode(["error" => "El archivo excede el tamaño permitido de 200MB"]);
            return;
        }

        if ($video['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(["error" => "Error en la subida del archivo", "code" => $video['error']]);
            return;
        }
    
        $nombreArchivo = basename($video['name']); 
        $destino = $ruta . "/" . $nombreArchivo; 
    
        if (move_uploaded_file($video['tmp_name'], $destino)) {
            echo json_encode(["mensaje" => "Video subido con éxito en '$ruta'."]);
        } else {
            echo json_encode(["error" => "Error al mover el archivo."]);
        }

    }

    /**
     * revisa la existencia de la carpeta
     *
     * @param $idSeccion id de la seccion
     * @param $ruta ruta de la capeta
     * 
     * @version 0.1.0
     */
    private function checkFolder($idSeccion, $ruta){
        if (!file_exists($ruta)) {
            if (mkdir($ruta, 0777, true)) {
                echo "Carpeta '$idSeccion' creada con éxito.";
            } else {
                echo "Error al crear la carpeta.";
            }
        } else {
            echo "La carpeta '$idSeccion' ya existe.";
        }
    }
}

?>
