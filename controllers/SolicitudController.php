<?php
require_once __DIR__ . "/../models/Solicitud.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";

class SolicitudController {
    private $solicitud;

    public function __construct() {
        $this->solicitud = new Solicitud();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    public function getSolicitudEstado($id) {
        $solicitud = $this->solicitud->getOne($id);

        if (!$solicitud) {
            echo json_encode(["error" => "Solicitud no encontrada"]);
            http_response_code(404);
            return;
        }

        $estado = $solicitud["Estado"];

        $responses = [
            "Pendiente" => ["message" => "La solicitud está pendiente", "code" => 200],
            "Aprobada" => ["message" => "La solicitud ha sido aprobada", "code" => 200],
            "Rechazada" => ["message" => "La solicitud ha sido rechazada", "code" => 200],
        ];

        if (isset($responses[$estado])) {
            http_response_code($responses[$estado]["code"]);
            echo json_encode(["estado" => $estado, "message" => $responses[$estado]["message"]]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Estado desconocido"]);
        }
    }
}
?>
