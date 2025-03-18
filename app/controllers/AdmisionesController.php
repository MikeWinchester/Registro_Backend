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
        if (empty($data["Primer_nombre"]) || empty($data["Segundo_nombre"]) || empty($data["Primer_apellido"]) ||
            empty($data["Segundo_apellido"]) || empty($data["Correo"]) || empty($data["Numero_identidad"]) ||
            empty($data["Numero_telefono"]) || !isset($data["CertificadoSecundaria"])) {
            // Si falta cualquier dato necesario, respondemos con código 444
            http_response_code(444);
            echo json_encode(["error" => "Faltan datos requeridos"]);
            return;
        }

        // Asignar los valores predeterminados si no se proporcionan
        //$CarreraID = isset($data["CarreraID"]) ? $data["CarreraID"] : 21;
        //$CarreraAlternativaID = isset($data["CarreraAlternativaID"]) ? $data["CarreraAlternativaID"] : 38;
        //$CentroRegionalID = isset($data["CentroRegionalID"]) ? $data["CentroRegionalID"] : 1;

        // Preparar los datos para la inserción
        $data_usr = [
            "Primer_nombre" => $data["Primer_nombre"],
            "Segundo_nombre" => $data["Segundo_nombre"],
            "Primer_apellido" => $data["Primer_apellido"],
            "Pegundo_apellido" => $data["Segundo_apellido"],
            "Correo" => $data["Correo"],
            "Numero_identidad" => $data["Numero_identidad"],
            "Numero_telefono" => $data["Numero_telefono"],
            "CarreraID" => 21,
            "CarreraAlternativaID" => 39,
            "CertificadoSecundaria" => isset($data["CertificadoSecundaria"]) ? $data["CertificadoSecundaria"] : null,
        ];

        // Llamada al modelo para insertar en la base de datos
        $result = $this->adm->customQueryInsert(
            "INSERT INTO admision (Primer_nombre, Segundo_nombre, Primer_apellido, Pegundo_apellido, Correo, Numero_identidad, Numero_telefono, CarreraID, CarreraAlternativaID, CertificadoSecundaria)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
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