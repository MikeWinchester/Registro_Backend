<?php
require_once __DIR__ . '/../core/Database.php';

abstract class BaseModel {
    protected mysqli $connection;
    protected string $table;
    protected string $primaryKey;
    
    public function __construct(string $table, $primaryKey) {
        $this->connection = Database::getInstance()->getConnection();
        $this->table = $table;
        $this->primaryKey = $primaryKey;
    }

    /**
     * Ejecuta consulta SELECT y retorna múltiples filas
     */
    protected function fetchAll(string $query, ?array $params = null): array {
        $stmt = $this->prepareAndExecute($query, $params);
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $stmt->close();
        return $data ?: [];
    }

    /**
     * Ejecuta consulta SELECT y retorna una fila
     */
    protected function fetchOne(string $query, ?array $params = null): ?array {
        $stmt = $this->prepareAndExecute($query, $params);
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $result->free();
        $stmt->close();
        return $data ?: null;
    }

    /**
     * Ejecuta consultas INSERT/UPDATE/DELETE
     */
    protected function executeWrite(string $query, ?array $params = null): int {
        $stmt = $this->prepareAndExecute($query, $params);
        $affectedRows = $this->connection->affected_rows;
        $stmt->close();
        return $affectedRows;
    }

    /**
     * Método interno para preparar y ejecutar statements
     */
    private function prepareAndExecute(string $query, ?array $params = null): mysqli_stmt {
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        
        if ($params && !empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception("Execute failed: " . $error);
        }
        
        return $stmt;
    }

    /* Métodos CRUD básicos */
    public function getAll(): array {
        $query = "SELECT * FROM {$this->table}";
        return $this->fetchAll($query);
    }

    public function getById($id): ?array {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->fetchOne($query, [$id]);
    }

    public function create(array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $this->executeWrite($query, array_values($data));
        return $this->connection->insert_id;
    }

    public function update($id, array $data): bool {
        $setClause = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $id;
        
        $query = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";
        return $this->executeWrite($query, $values) > 0;
    }

    public function delete($id): bool {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->executeWrite($query, [$id]) > 0;
    }

    /* Transacciones */
    public function beginTransaction(): void {
        $this->connection->begin_transaction();
    }

    public function commit(): void {
        $this->connection->commit();
    }

    public function rollback(): void {
        $this->connection->rollback();
    }
}