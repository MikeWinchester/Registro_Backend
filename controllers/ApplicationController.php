<?php
require_once __DIR__ . "/../models/Application.php";

class ApplicationController extends BaseController {
    private $solicitudModel;

    public function __construct() {
        $this->solicitudModel = new Application();
        header("Content-Type: application/json");
    }

    public function getByCode($request) {
        try {
            $code = $request->getRouteParam(0);
            
            if (empty($code)) {
                return $this->jsonResponse([
                    'success' => false, 
                    'message' => 'Código de solicitud requerido'
                ], 400);
            }
    
            $solicitud = $this->solicitudModel->getByCode($code);
            
            if (!$solicitud) {
                return $this->jsonResponse([
                    'success' => false, 
                    'message' => 'Solicitud no encontrada'
                ], 404);
            }
            
            // Asegurar formato compatible con el frontend
            $response = [
                'success' => true,
                'solicitud' => [
                    'numero' => $solicitud['codigo'],
                    'nombre' => trim($solicitud['primer_nombre']) . ' ' . 
                                ($solicitud['segundo_nombre'] ?? '') . ' ' . 
                                $solicitud['primer_apellido'] . 
                                ($solicitud['segundo_apellido'] ? ' ' . $solicitud['segundo_apellido'] : ''),
                    'documento' => $solicitud['numero_documento'],
                    'correo' => $solicitud['correo'],
                    'telefono' => $solicitud['numero_telefono'],
                    'centro' => $solicitud['nombre_centro'],
                    'carrera1' => $solicitud['carrera_principal'],
                    'carrera2' => $solicitud['carrera_secundaria'],
                    'certificado' => !empty($solicitud['certificado_secundaria']),
                    'estado' => strtolower($solicitud['estado']),
                    'observaciones' => $solicitud['observaciones'] ?? 'No hay observaciones'
                ]
            ];
            
            return $this->jsonResponse($response);
            
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
}