<?php
require_once __DIR__ . '/../core/Database.php';

abstract class BaseModel {
    protected mysqli $connection;
    protected string $table;
    protected string $primaryKey;
    protected string $uuidColumn = 'id';
    
    public function __construct(string $table, string $primaryKey) {
        $this->connection = Database::getInstance()->getConnection();
        $this->table = $table;
        $this->primaryKey = $primaryKey;
    }

    /**
     * Ejecuta consulta SELECT y retorna múltiples filas con paginación
     */
    protected function fetchAll(string $query, ?array $params = null, int $page = 1, int $perPage = 10): array {
        // Añadir paginación si no está presente
        if (stripos($query, 'LIMIT') === false) {
            $offset = ($page - 1) * $perPage;
            $query .= " LIMIT $offset, $perPage";
        }
        
        $stmt = $this->prepareAndExecute($query, $params);
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $stmt->close();
        return $data ?: [];
    }

    /**
     * Obtiene el total de registros para una consulta
     */
    protected function getTotalRecords(string $query, ?array $params = null): int {
        // Convertir a consulta de conteo
        if (stripos($query, 'SELECT COUNT(*)') === false) {
            $query = "SELECT COUNT(*) as total FROM (" . $query . ") as count_table";
        }
        
        $stmt = $this->prepareAndExecute($query, $params);
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $result->free();
        $stmt->close();
        return $data['total'] ?? 0;
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
    public function getAll(int $page = 1, int $perPage = 10): array {
        $query = "SELECT * FROM {$this->table}";
        return $this->fetchAll($query, null, $page, $perPage);
    }

    public function getById(string $uuid): ?array {
        $query = "SELECT * FROM {$this->table} WHERE {$this->uuidColumn} = ? LIMIT 1";
        return $this->fetchOne($query, [$uuid]);
    }

    public function getByPrimaryKey(string $key): ?array {
        $query = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        return $this->fetchOne($query, [$key]);
    }

    public function create(array $data): string {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $this->executeWrite($query, array_values($data));
        
        /* Obtener el UUID del registro insertado
        $id = $this->connection->insert_id;
        $uuid = $this->fetchOne(
            "SELECT {$this->uuidColumn} FROM {$this->table} WHERE {$this->primaryKey} = ?", 
            [$id]
        );*/
        
        //return $uuid[$this->uuidColumn] ?? '';
        return $this->connection->insert_id;
    }

    public function update(string $uuid, array $data): bool {
        $setClause = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $uuid;
        
        $query = "UPDATE {$this->table} SET {$setClause} WHERE {$this->uuidColumn} = ?";
        return $this->executeWrite($query, $values) > 0;
    }

    public function delete(string $uuid): bool {
        $query = "DELETE FROM {$this->table} WHERE {$this->uuidColumn} = ?";
        return $this->executeWrite($query, [$uuid]) > 0;
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