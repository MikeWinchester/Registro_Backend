<?php
class Database {
    private $conn;

    public function __construct() {
        $config = $GLOBALS['config'];

        $this->conn = new mysqli(
            $config["DB_HOST"],
            $config["DB_USER"],
            $config["DB_PASS"],
            $config["DB_NAME"]
        );

        if ($this->conn->connect_error) {
            die(json_encode(["error" => "Error de conexión: " . $this->conn->connect_error]));
            var_dump("No se conecto");
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
