<?php

require_once __DIR__ . "/../models/Admisiones.php";
require_once __DIR__ . "/../core/Cors.php"; 

$router = new Router;

echo json_encode([
    'status' => 'success',
    'message' => 'Tu madre tiene una polla, que ya la quisiera yo'
]);

class AdmisionController {
    private $adm;

    public function __construct() {
        $this->adm = new Admision();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    public function createAdmision() {
        // Obtener los datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents("php://input"), true);

        // Validar los datos requeridos
        if (!isset($data["NombreCompleto"]) || !isset($data["Identidad"]) || !isset($data["Correo"]) ||
            !isset($data["Pass"]) || !isset($data['ES_Revisor']) || !isset($data["NumeroCuenta"])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos requeridos"]);
            return;
        }

        // Hash
        $data["Pass"] = password_hash($data["Pass"], PASSWORD_DEFAULT);
        
        $data_usr = [
            "NombreCompleto" => $data["NombreCompleto"],
            "Identidad" => $data["Identidad"],
            "Correo" => $data["Correo"],
            "Pass" => $data["Pass"],
            "Rol" => $data["Rol"],
            "NumeroCuenta" => $data["NumeroCuenta"],
            "Telefono" => isset($data["Telefono"]) ? $data["Telefono"] : null,
            "ES_Revisor" => $data["ES_Revisor"],
        ];

        $result = $this->adm->customQueryInsert(
            "INSERT INTO usuario (NombreCompleto, Identidad, Correo, Pass, Rol, NumeroCuenta, Telefono, ES_Revisor)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            array_values($data_usr)
        );

        if ($result) {
            http_response_code(201);
            echo json_encode(["success" => "Admisión creada exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear la admisión"]);
        }
    }
}
?>