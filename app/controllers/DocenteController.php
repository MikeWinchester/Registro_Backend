<?php

require_once __DIR__ . "/../models/Docente.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";

class DocenteController {
    private $docente;

    public function __construct() {
        $this->docente = new Docente();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    /**
     * Funcion para crear al docente y a su usuario
     *
     * @version 0.1.0
     */
    public function createDocente() {
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!isset($data["NombreCompleto"]) || !isset($data["Identidad"]) || !isset($data["Correo"]) ||
            !isset($data["Pass"]) || !isset($data['ES_Revisor']) || !isset($data["NumeroCuenta"]) || 
            !isset($data["CentroRegionalID"])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos requeridos"]);
            return;
        }
    
        // Hashear la contraseña
        $data["Pass"] = password_hash($data["Pass"], PASSWORD_DEFAULT);
    
        // Separar los datos de usuario y docente
        $data_usr = [
            "NombreCompleto" => $data["NombreCompleto"],
            "Identidad" => $data["Identidad"],
            "Correo" => $data["Correo"],
            "Pass" => $data["Pass"],
            "Rol" => "Docente",
            "NumeroCuenta" => $data["NumeroCuenta"],
            "Telefono" => isset($data["Telefono"]) ? $data["Telefono"] : null,
            "ES_Revisor" => $data["ES_Revisor"]
        ];
    
        // Insertar usuario
        $this->docente->customQueryInsert(
            "INSERT INTO Usuario (NombreCompleto, Identidad, Correo, Pass, Rol, NumeroCuenta, Telefono, ES_Revisor)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            array_values($data_usr)
        );
    
        // Obtener el UsuarioID recién insertado
        $usuario = $this->docente->customQuery(
            "SELECT UsuarioID FROM Usuario WHERE Correo=?",
            [$data['Correo']]
        );
    
        if (!$usuario || count($usuario) == 0) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener UsuarioID"]);
            return;
        }
    
        $data_doc = [
            "UsuarioID" => $usuario[0]['UsuarioID'],
            "CentroRegionalID" => $data["CentroRegionalID"],
            "CarreraID" => $data["CarreraID"],
            "CodigoEmpleado" => $data["CodigoEmpleado"]
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
     * @version 0.1.0
     */
    public function getDocente($idDocente){
        
        #AuthMiddleware::authMiddleware();

        $sql = "SELECT usr.NombreCompleto, usr.Correo, usr.NumeroCuenta, doc.CodigoEmpleado, cr.NombreCentro, crr.NombreCarrera
        FROM Docente AS doc
        INNER JOIN Usuario AS usr
        ON doc.UsuarioID = usr.UsuarioID
        INNER JOIN CentroRegional AS cr
        ON doc.CentroRegionalID = cr.CentroRegionalID
        INNER JOIN Carrera as crr
        on doc.CarreraID = crr.CarreraID
        WHERE DocenteID = ?";

        $result = $this->docente->customQuery($sql, [$idDocente]);

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
     * @version 0.1.1
     */
    public function getAllDocentes(){
        #AuthMiddleware::authMiddleware();

        $sql = "SELECT usr.NombreCompleto, usr.NumeroCuenta, cr.NombreCentro FROM Docente AS doc
        INNER JOIN Usuario AS usr
        ON doc.UsuarioID = usr.UsuarioID
        INNER JOIN CentroRegional AS cr
        ON doc.CentroRegionalID = cr.CentroRegionalID";

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
     * @version 0.1.1
     */
    public function getDocentesByCentro(){

        $header = getallheaders();

        if(!isset($header['CentroRegionalID'])){
            http_response_code(400);
            echo json_encode(["error" => "CentroRegionalID es requerido en el header"]);
            return;
        }
    
        $centroID = $header['CentroRegionalID'];

        $sql = "SELECT usr.NombreCompleto
        FROM Docente AS doc
        INNER JOIN Usuario AS usr
        ON doc.UsuarioID = usr.UsuarioID
        WHERE doc.CentroRegionalID = ?";
        
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
