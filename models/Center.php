<?php
require_once __DIR__ . '/BaseModel.php';

class Center extends BaseModel {
    public function __construct() {
        parent::__construct('tbl_centro_regional', 'centro_regional_id');
    }

    public function getByCode(string $codigoCentro): ?array {
        return $this->fetchOne(
            "SELECT * FROM {$this->table} WHERE codigo_centro = ? LIMIT 1",
            [$codigoCentro]
        );
    }
}