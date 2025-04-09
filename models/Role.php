<?php
require_once __DIR__ . '/BaseModel.php';

class Role extends BaseModel {
    public function __construct() {
        parent::__construct('tbl_rol', 'rol_id');
    }
    
    public function getByName(string $name): ?array {
        return $this->fetchOne(
            "SELECT * FROM {$this->table} WHERE nombre_rol = ? LIMIT 1",
            [$name]
        );
    }
}