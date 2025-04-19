<?php
require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../models/Application.php";
require_once __DIR__ . "/../models/Reviewer.php";

class ReviewerController extends BaseController {
    private $solicitudModel;
    private $revisorModel;

    public function __construct() {
        parent::__construct();
        $this->solicitudModel = new Application();
        $this->revisorModel = new Reviewer();
    }

    /**
     * Obtiene las solicitudes del revisor con paginación
     * GET /api/revisores/{usuarioId}/solicitudes
     */
    public function getSolicitudesRevisor($request) {
        try {
            $usuarioUuid = $request->getRouteParam(0);
            $page = $request->getQueryParam('page', 1);
            $perPage = $request->getQueryParam('per_page', 10);
            $estado = strtolower($request->getQueryParam('estado', 'todas'));
            
            // Validar estado
            if (!in_array($estado, ['todas', 'aprobada', 'rechazada', 'pendiente'])) {
                $estado = 'todas';
            }
    
            // Obtener UUID del revisor
            $revisorUuid = $this->revisorModel->obtenerRevisorId($usuarioUuid);
            
            if (!$revisorUuid) {
                throw new Exception("No se encontró el perfil de revisor", 404);
            }
    
            // Obtener solicitudes con filtros
            $asignadas = $this->solicitudModel->getSolicitudesAsignadas($revisorUuid, $page, $perPage, $estado);
            $totalAsignadas = $this->solicitudModel->getTotalSolicitudesAsignadas($revisorUuid, $estado);
            
            $todas = $this->solicitudModel->getTodasLasSolicitudes($page, $perPage, $estado);
            $totalTodas = $this->solicitudModel->getTotalTodasLasSolicitudes($estado);
    
            $this->jsonResponse([
                'success' => true,
                'asignadas' => $asignadas,
                'total_asignadas' => $totalAsignadas,
                'todas' => $todas,
                'total_todas' => $totalTodas,
                'current_page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($totalAsignadas / $perPage)
            ]);
    
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Actualiza una solicitud
     * POST /api/revisores/actualizar
     */
    public function actualizarSolicitud($request) {
        try {
            $data = $request->getBody();

            error_log("Datos recibidos para actualización: " . print_r($data, true));

            $this->validateRequiredFields($data, ['solicitud_uuid', 'estado', 'revisor_uuid']);
    
            // Actualizar la solicitud
            $success = $this->solicitudModel->actualizarEstado(
                $data['solicitud_uuid'],
                $this->convertirEstado($data['estado']),
                $data['observaciones'] ?? null
            );
    
            if (!$success) {
                throw new Exception("No se pudo actualizar la solicitud");
            }
    
            // Obtener y devolver los datos actualizados
            $solicitudActualizada = $this->solicitudModel->getById($data['solicitud_uuid']);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Solicitud actualizada correctamente',
                'solicitud' => $solicitudActualizada
            ]);
    
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    public function getCertificado($request) {
        try {
            $solicitudUuid = $request->getRouteParam(0);
            
            // Obtener el nombre del archivo del certificado desde la base de datos
            $nombreArchivo = $this->revisorModel->getCertificadoAspirante($solicitudUuid);
            
            if (!$nombreArchivo) {
                throw new Exception("Certificado no encontrado", 404);
            }

            // Ruta completa al archivo
            $rutaCertificado = __DIR__ . '/../data/uploads/certificados/' . $nombreArchivo;
            
            // Verificar que el archivo exista
            if (!file_exists($rutaCertificado)) {
                throw new Exception("Archivo de certificado no encontrado", 404);
            }

            // Leer el contenido del archivo y convertirlo a base64
            $contenido = file_get_contents($rutaCertificado);
            $base64 = base64_encode($contenido);
            
            // Determinar el tipo MIME
            $mime = mime_content_type($rutaCertificado);
            
            $this->jsonResponse([
                'success' => true,
                'certificado' => $base64,
                'mime_type' => $mime,
                'nombre_archivo' => $nombreArchivo
            ]);

        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    private function convertirEstado($estadoFrontend): string {
        return match(strtolower($estadoFrontend)) {
            'aprobada' => 'Aprobada',
            'rechazada' => 'Rechazada',
            default => 'Pendiente'
        };
    }
}