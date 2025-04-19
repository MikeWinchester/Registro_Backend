<?php
require_once __DIR__ . '/BaseModel.php';

class Reviewer extends BaseModel {
    public function __construct() {
        parent::__construct('tbl_revisor', 'revisor_id');
    }

    public function obtenerRevisorId(string $usuarioUuid): ?string {
        $sql = "SELECT r.id as revisor_uuid 
                FROM tbl_revisor r
                JOIN tbl_usuario u ON r.usuario_id = u.usuario_id
                WHERE u.id = ?";
        
        $result = $this->fetchOne($sql, [$usuarioUuid]);
        return $result['revisor_uuid'] ?? null;
    }

    public function getCertificadoAspirante(string $solicitudUuid): ?string {
        $sql = "SELECT a.certificado_secundaria
                FROM tbl_admision a
                JOIN tbl_solicitud s ON a.admision_id = s.solicitud_id
                WHERE s.id = ?";
        
        $result = $this->fetchOne($sql, [$solicitudUuid]);
        return $result['certificado_secundaria'] ?? null;
    }
}