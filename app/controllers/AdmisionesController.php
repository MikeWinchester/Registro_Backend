<?php

require_once __DIR__ . "/../models/Admisiones.php";
require_once __DIR__ . "/../core/Cors.php"; 

class AdmisionController {
    private $adm;

    public function __construct() {
        $this->adm = new Admisiones();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    public function createAdmision() {
        // Obtener los datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents("php://input"), true);

        // Imprimir los datos recibidos para depuración
        error_log(print_r($data, true)); // Esto imprimirá los datos en el archivo de log de PHP

        // Validar los datos requeridos
        if (empty($data["NombreCompleto"]) || empty($data["Identidad"]) || empty($data["Correo"]) ||
            empty($data["Pass"]) || !isset($data["Es_Revisor"]) || empty($data["NumeroCuenta"])) {
            // Si falta cualquier dato necesario, respondemos con código 444
            http_response_code(444);
            echo json_encode(["error" => "Faltan datos requeridos"]);
            return;
        }

        // Hash de la contraseña para almacenamiento seguro
        $data["Pass"] = password_hash($data["Pass"], PASSWORD_DEFAULT);

        // Preparar los datos para la inserción
        $data_usr = [
            "NombreCompleto" => $data["NombreCompleto"],
            "Identidad" => $data["Identidad"],
            "Correo" => $data["Correo"],
            "Pass" => $data["Pass"],
            "Rol" => isset($data["Rol"]) ? $data["Rol"] : 'Estudiante',  // Asegurarse de que siempre haya un rol
            "NumeroCuenta" => $data["NumeroCuenta"],
            "Telefono" => isset($data["Telefono"]) ? $data["Telefono"] : null,
            "Es_Revisor" => $data["Es_Revisor"], // Asegurarse de que "Es_Revisor" esté correctamente asignado
        ];

        // Llamada al modelo para insertar en la base de datos
        $result = $this->adm->customQueryInsert(
            "INSERT INTO usuario (NombreCompleto, Identidad, Correo, Pass, Rol, NumeroCuenta, Telefono, ES_Revisor)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            array_values($data_usr)
        );

        // Responder con éxito o error según el resultado
        if ($result) {
            http_response_code(201);
            echo json_encode(["success" => "Admisión creada exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear la admisión"]);
        }
    }
}