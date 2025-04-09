<?php
require_once __DIR__ . '/../config/constants.php';

class Database {
    private $connection;
    private static $instance = null;
    
    private function __construct() {
        $this->connect();
    }
    
    private function connect(): void {
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->connection->connect_error) {
            throw new Exception("Connection failed: " . $this->connection->connect_error);
        }
        
        $this->connection->set_charset("utf8mb4");
    }
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        if (!self::$instance->connection || !self::$instance->connection->thread_id) {
            self::$instance->connection->close();
            self::$instance->connect();
        }
        
        return self::$instance;
    }
    
    public function getConnection(): mysqli {
        if (!$this->connection || !$this->connection->thread_id) {
            $this->connection->close();
            $this->connect();
        }
        return $this->connection;
    }
    
    public function __destruct() {
        if ($this->connection && $this->connection->thread_id) {
            $this->connection->close();
        }
    }
}