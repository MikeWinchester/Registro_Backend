<?php
class Database {
    private $conn;

    public function __construct() {
        $config = require_once __DIR__ . "/../../config.php";

        $this->conn = new mysqli(
            $config["DB_HOST"],
            $config["DB_USER"],
            $config["DB_PASS"],
            $config["DB_NAME"]
        );

        var_dump($config["DB_HOST"]);

        if ($this->conn->connect_error) {
            die(json_encode(["error" => "Error de conexión: " . $this->conn->connect_error]));
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>

