<?php
require_once __DIR__ . '/BaseModel.php';

class Career extends BaseModel {
    public function __construct() {
        parent::__construct('tbl_carrera', 'carrera_id');
    }

    public function getByCode(string $codigoCarrera): ?array {
        return $this->fetchOne(
            "SELECT * FROM {$this->table} WHERE codigo_carrera = ? LIMIT 1",
            [$codigoCarrera]
        );
    }

    public function getByCentroRegional(int $centroId): array {
        return $this->fetchAll(
            "SELECT c.* FROM {$this->table} c
             JOIN tbl_carrera_x_centro_regional cc ON c.carrera_id = cc.carrera_id
             WHERE cc.centro_regional_id = ?",
            [$centroId]
        );
    }

    public function existsInCenter(int $carreraId, int $centroId): bool {
        $result = $this->fetchOne(
            "SELECT 1 FROM tbl_carrera_x_centro_regional 
             WHERE carrera_id = ? AND centro_regional_id = ? LIMIT 1",
            [$carreraId, $centroId]
        );
        
        return !empty($result);
    }

    public function obtenerCarreraEstu($estudianteid){

        $sql = "SELECT carrera_id as carrera
        FROM tbl_estudiante
        WHERE estudiante_id = ?";

        return $this->fetchOne($sql, [$estudianteid]);
    }

}