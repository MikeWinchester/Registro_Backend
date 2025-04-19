<?php
require_once __DIR__ . '/BaseModel.php';

class Admission extends BaseModel {
    public function __construct() {
        parent::__construct('tbl_admision', 'admision_id');
    }

    public function createAdmision(array $data): array {
        $this->beginTransaction();
        
        try {
            // 1. Crear la admisión
            $admisionId = $this->create($data);
            if (!$admisionId) {
                throw new Exception("Error al crear el registro de admisión");
            }

            // 2. Asignar revisor automáticamente
            $asignacionId = $this->assignReviewer($admisionId);
            
            $this->commit();
            
            return [
                'admision_id' => $admisionId,
                'solicitud_id' => $admisionId,
                'asignacion_id' => $asignacionId
            ];
            
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }


    public function getByUuid(string $uuid): ?array {
        return $this->fetchOne(
            "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1", 
            [$uuid]
        );
    }

    private function assignReviewer(int $solicitudId): int {
        // Obtener revisor con menor carga de trabajo
        $revisor = $this->fetchOne(
            "SELECT r.revisor_id 
             FROM tbl_revisor r
             LEFT JOIN tbl_asignacion_revisor ar ON r.revisor_id = ar.revisor_id
             LEFT JOIN tbl_solicitud s ON ar.solicitud_id = s.solicitud_id AND s.estado = 'Pendiente'
             GROUP BY r.revisor_id
             ORDER BY COUNT(ar.asignacion_id) ASC
             LIMIT 1"
        );

        if (!$revisor) {
            throw new Exception("No hay revisores disponibles para asignar");
        }

        // Asignar la solicitud al revisor
        return $this->executeWrite(
            "INSERT INTO tbl_asignacion_revisor (solicitud_id, revisor_id, fecha_asignacion)
             VALUES (?, ?, NOW())",
            [$solicitudId, $revisor['revisor_id']]
        );
    }

}