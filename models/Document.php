<?php
require_once __DIR__ . '/BaseModel.php';

class Document extends BaseModel {
    public function __construct() {
        parent::__construct('tbl_documento', 'documento_id');
    }

    public function getByNumber(string $numeroDocumento): ?array {
        return $this->fetchOne(
            "SELECT * FROM {$this->table} WHERE numero_documento = ? LIMIT 1",
            [$numeroDocumento]
        );
    }

    public function createDocumento(string $numeroDocumento, int $tipoDocumentoId): ?int {
        $result = $this->executeWrite(
            "INSERT INTO {$this->table} (numero_documento, tipo_documento_id) VALUES (?, ?)",
            [$numeroDocumento, $tipoDocumentoId]
        );

        return $result ? $this->connection->insert_id : null;
    }
}