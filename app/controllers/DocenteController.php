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

    //funcion para crear docente
    public function createDocente() {
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!isset($data["NombreCompleto"]) || !isset($data["Identidad"]) || !isset($data["Correo"]) ||
            !isset($data["Pass"]) || !isset($data["Rol"]) || !isset($data['ES_Revisor']) || !isset($data["NumeroCuenta"]) || 
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
            "Rol" => $data["Rol"],
            "Telefono" => isset($data["Telefono"]) ? $data["Telefono"] : null,
            "ES_Revisor" => $data["ES_Revisor"]
        ];
    
        // Insertar usuario
        $this->docente->customQueryInsert(
            "INSERT INTO Usuario (NombreCompleto, Identidad, Correo, Pass, Rol, Telefono, ES_Revisor)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
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
            "NumeroCuenta" => $data["NumeroCuenta"],
            "CentroRegionalID" => $data["CentroRegionalID"],
        ];
    
        // Insertar docente
        if ($this->docente->create($data_doc)) {
            echo json_encode(["message" => "Docente creado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear Docente"]);
        }
    }
    
    public function getDocente($id){
        
    }


}

?>
