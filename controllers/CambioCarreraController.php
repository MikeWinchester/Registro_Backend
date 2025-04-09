<?php

require_once __DIR__ . "/../models/CambioCarrera.php";
require_once __DIR__ . "/BaseController.php";

class CambioCarrera extends BaseController{
    private $CC;

    public function __construct() {
        parent::__construct();
        $this->CC = new CCarrera();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }


public function getSolicitudes() {

    // Ejecutamos la consulta
    $result = $this->CC->obtenerSolicitudes();

    // Verificamos si la consulta devuelve resultados
    if ($result) {
        http_response_code(200);
        echo json_encode(["message" => "Encontradas!!!!!", "data" => $result]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "No Encontradas >>>>::::::::VVVVV"]);
    }
}

public function responderSolicitud() {
    // Obtener los datos en el cuerpo de la solicitud (JSON)
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificar que los datos estén completos
    if (isset($data['numero_cuenta']) && isset($data['estado'])) {
        $numeroCuenta = $data['numero_cuenta'];  // Usar el número de cuenta recibido
        $estado = $data['estado'];
        error_log($numeroCuenta);
        error_log($estado);

        // Obtener el estudiante_id
        $estudiante_id_result = $this->CC->obtenerIdEstudiante([$data['numero_cuenta']]);

        // Verificar si el resultado contiene el estudiante_id
        if (isset($estudiante_id_result['estudiante_id'])) {
            $estudiante_id = $estudiante_id_result['estudiante_id'];  // Extraemos el estudiante_id

            // Log para verificar
            error_log("Estudiante ID: " . $estudiante_id);

            // Actualizar el estado de la solicitud
            
            $resultSec = $this->CC->actualizarEstadoSolicitud([$estado, $estudiante_id]);

            // Verificar si la actualización fue exitosa
            if ($resultSec) {
                error_log("La solicitud ha sido actualizada correctamente.");
            } else {
                error_log("Error al actualizar la solicitud.");
            }
        } else {
            error_log("No se encontró el estudiante con el número de cuenta: " . $numeroCuenta);
        }
    }
}

}