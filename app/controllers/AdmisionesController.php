<?php
require_once __DIR__ . "/../core/Cors.php";
require_once __DIR__ . "/../models/Admisiones.php";

class AdmisionesController {
    private $admisiones;

    public function __construct() {
        $this->admisiones = new Admisiones();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    public function createAdmisiones() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data["NombreCompleto"]) || empty($data["Identidad"]) || empty($data["Correo"]) || empty($data["Pass"]) || empty($data["NumeroCuenta"])) {
            http_response_code(400); // Código de respuesta 400 para Bad Request
            echo json_encode(["error" => "Datos incompletos."]);
            return;
        }
        $data["Pass"] = password_hash($data["Pass"], PASSWORD_DEFAULT);
        $rol = "Estudiante";  // Valor fijo para el rol, puede cambiarse si es necesario
        $data_usr = [
            "NombreCompleto" => $data["NombreCompleto"],
            "Identidad" => $data["Identidad"],
            "Correo" => $data["Correo"],
            "Pass" => $data["Pass"],
            "Rol" => $rol,
            "NumeroCuenta" => $data["NumeroCuenta"],
            "Telefono" => isset($data["Telefono"]) ? $data["Telefono"] : null,
            "Es_Revisor" => isset($data["Es_Revisor"]) ? $data["Es_Revisor"] : 0
        ];

        $query = "INSERT INTO usuario (NombreCompleto, Identidad, Correo, Pass, Rol, NumeroCuenta, Telefono, Es_Revisor) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $inserted = $this->admisiones->execute_query($query, array_values($data_usr));

        if ($inserted) {
            echo json_encode(["message" => "Admisión creada correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear el usuario"]);
        }
    }
}
?>