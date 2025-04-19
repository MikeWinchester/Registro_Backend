<?php
require_once __DIR__ . '/BaseModel.php';

class Application extends BaseModel {
    public function __construct() {
        parent::__construct('tbl_solicitud', 'solicitud_id');
    }

    public function createSolicitud(int $admisionId): bool {
        return $this->executeWrite(
            "INSERT INTO {$this->table} (solicitud_id, admision_id) VALUES (?, ?)",
            [$admisionId, $admisionId]
        ) > 0;
    }

    public function getByCode(string $codigo): ?array {
        return $this->fetchOne(
            "SELECT s.*, a.*, cr.nombre_centro, c1.nombre_carrera as carrera_principal, 
             c2.nombre_carrera as carrera_secundaria, d.numero_documento, td.descripcion as tipo_documento
             FROM {$this->table} s
             JOIN tbl_admision a ON s.admision_id = a.admision_id
             JOIN tbl_centro_regional cr ON a.centro_regional_id = cr.centro_regional_id
             JOIN tbl_carrera c1 ON a.carrera_id = c1.carrera_id
             JOIN tbl_carrera c2 ON a.carrera_secundaria_id = c2.carrera_id
             JOIN tbl_documento d ON a.documento_id = d.documento_id
             JOIN tbl_tipo_documento td ON d.tipo_documento_id = td.tipo_documento_id
             WHERE s.codigo = ?",
            [$codigo]
        );
    }

    public function getByAdmisionId(int $admisionId): ?array {
        return $this->fetchOne(
            "SELECT * FROM {$this->table} WHERE admision_id = ? LIMIT 1",
            [$admisionId]
        );
    }

    public function getSolicitudesAsignadas(string $revisorUuid, int $page = 1, int $perPage = 10, string $estado = 'todas'): array {
        $query = "SELECT 
                s.id as solicitud_uuid,
                s.codigo, 
                LOWER(s.estado) AS estado,
                s.observaciones,
                CONCAT(a.primer_nombre, ' ', a.primer_apellido) AS nombre_completo,
                d.numero_documento,
                c1.nombre_carrera AS carrera_principal,
                a.fecha_registro,
                a.correo,
                a.numero_telefono,
                c2.nombre_carrera AS carrera_secundaria,
                cr.nombre_centro AS centro_regional,
                a.certificado_secundaria
            FROM tbl_solicitud s
            JOIN tbl_admision a ON s.solicitud_id = a.admision_id
            JOIN tbl_documento d ON a.documento_id = d.documento_id
            JOIN tbl_carrera c1 ON a.carrera_id = c1.carrera_id
            LEFT JOIN tbl_carrera c2 ON a.carrera_secundaria_id = c2.carrera_id
            LEFT JOIN tbl_centro_regional cr ON a.centro_regional_id = cr.centro_regional_id
            JOIN tbl_asignacion_revisor ar ON s.solicitud_id = ar.solicitud_id
            JOIN tbl_revisor r ON ar.revisor_id = r.revisor_id
            WHERE r.id = ?";
        
        $params = [$revisorUuid];
        
        if ($estado !== 'todas') {
            $query .= " AND s.estado = ?";
            $params[] = ucfirst($estado);
        }
        
        $query .= " LIMIT " . (($page - 1) * $perPage) . ", $perPage";
        
        return $this->fetchAll($query, $params);
    }

    public function getTotalSolicitudesAsignadas(string $revisorUuid): int {
        $query = "SELECT COUNT(*) as total
            FROM tbl_solicitud s
            JOIN tbl_asignacion_revisor ar ON s.solicitud_id = ar.solicitud_id
            JOIN tbl_revisor r ON ar.revisor_id = r.revisor_id
            WHERE r.id = ?";
        
        return $this->getTotalRecords($query, [$revisorUuid]);
    }

    public function getTodasLasSolicitudes(int $page = 1, int $perPage = 10, string $estado = 'todas'): array {
        $query = "SELECT 
                s.id as solicitud_uuid,
                s.codigo, 
                LOWER(s.estado) AS estado,
                CONCAT(a.primer_nombre, ' ', a.primer_apellido) AS nombre_completo,
                d.numero_documento,
                COALESCE(c1.nombre_carrera, 'No especificada') AS carrera_principal,
                a.fecha_registro,
                COALESCE(u.nombre_completo, 'Sin asignar') AS revisor_nombre
            FROM tbl_solicitud s
            JOIN tbl_admision a ON s.solicitud_id = a.admision_id
            JOIN tbl_documento d ON a.documento_id = d.documento_id
            LEFT JOIN tbl_carrera c1 ON a.carrera_id = c1.carrera_id
            LEFT JOIN tbl_asignacion_revisor ar ON s.solicitud_id = ar.solicitud_id
            LEFT JOIN tbl_revisor r ON ar.revisor_id = r.revisor_id
            LEFT JOIN tbl_usuario u ON r.usuario_id = u.usuario_id";
        
        $params = [];
        
        if ($estado !== 'todas') {
            $query .= " WHERE s.estado = ?";
            $params[] = ucfirst($estado);
        }
        
        $query .= " LIMIT " . (($page - 1) * $perPage) . ", $perPage";
        
        return $this->fetchAll($query, $params);
    }

    public function getTotalTodasLasSolicitudes(): int {
        $query = "SELECT COUNT(*) as total FROM tbl_solicitud";
        return $this->getTotalRecords($query);
    }

    public function actualizarEstado(string $solicitudUuid, string $estado, ?string $observaciones = null): bool {
        return $this->executeWrite(
            "UPDATE tbl_solicitud s
             JOIN tbl_admision a ON s.admision_id = a.admision_id
             SET s.estado = ?, s.observaciones = ?
             WHERE s.id = ?",
            [$estado, $observaciones, $solicitudUuid]
        ) > 0;
    }
}