<?php
require_once __DIR__ . "/Database.php";

class Model {
    protected $conn;
    protected $table;

    public function __construct($table) {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->table = $table;
    }

    // Obtener todos los registros
    public function getAll() {
        $sql = "SELECT * FROM `$this->table`";
        return $this->conn->execute_query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // Obtener un solo registro por IDs
    public function getOne($id) {
        $sql = "SELECT * FROM `$this->table` WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Crear un nuevo registro
    public function create($fields) {
        $columns = implode(", ", array_keys($fields));
        $placeholders = implode(", ", array_fill(0, count($fields), "?"));
        $sql = "INSERT INTO `$this->table` ($columns) VALUES ($placeholders)";
        
        $stmt = $this->conn->prepare($sql);
        $types = str_repeat("s", count($fields));
        $stmt->bind_param($types, ...array_values($fields));

        return $stmt->execute();
    }

    public function update($id, $fields) {

        $set = implode(", ", array_map(function ($key) {
            return "$key = ?";
        }, array_keys($fields)));

        $sql = "UPDATE `$this->table` SET $set WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        $values = array_values($fields);
        $values[] = $id;

        $types = "";
        foreach ($values as $value) {
            $types .= is_int($value) ? "i" : (is_float($value) ? "d" : "s");
        }

        $stmt->bind_param($types, ...$values);

        return $stmt->execute();
    }

    // Eliminar un registro por ID
    public function delete($id) {
        $sql = "DELETE FROM `$this->table` WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    // Contar registros en la tabla
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM `$this->table`";
        return $this->conn->execute_query($sql)->fetch_assoc()["total"];
    }

    // Buscar registros por una columna específica
    public function findBy($column, $value) {
        $sql = "SELECT * FROM `$this->table` WHERE `$column` = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $value);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Ejecutar consultas personalizadas con parámetros
    public function customQuery($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($params)) {
            $types = str_repeat("s", count($params));
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Ejecutar consultas personalizadas para insert con parámetros
    public function customQueryInsert($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
    
        if (!$stmt) {
            error_log("Error en la consulta SQL: " . $this->conn->error);
            return false;
        }
    
        if (!empty($params)) {
            $types = str_repeat("s", count($params));
            $stmt->bind_param($types, ...$params);
        }
    
        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            return false;
        }
    
        $result = $stmt->get_result();
    
        if (!$result) {
            return false;
        }
    
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
}
?>
