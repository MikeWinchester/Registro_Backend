<?php
require_once __DIR__ . "/../core/Database.php";
require_once __DIR__ . "/../core/Cors.php";

class AuthController {

    private $conn;
    private $secret_key;

    public function __construct() {
        $database = new Database();
        $config = require_once __DIR__ . "/../../config.php";
        $this->conn = $database->getConnection();
        $this->secret_key = $config["SECRET_KEY"];

        header("Content-Type: application/json"); // Estandarizar el tipo de respuesta JSON
    }

    public function login() {
        header("Content-Type: application/json"); // Asegurar que siempre devuelve JSON
    
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data["NumeroCuenta"]) || !isset($data["Pass"])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos"]);
            return;
        }
    
        $NumeroCuenta = $data["NumeroCuenta"];
        $Pass = $data["Pass"];
    
        $user = $this->authenticateUser($NumeroCuenta, $Pass);
        if (!$user) {
            http_response_code(401);
            echo json_encode(["error" => "Credenciales incorrectas"]);
            return;
        }
    
        $payload = [
            "id" => $user["UsuarioID"],
            "NumeroCuenta" => $user["NumeroCuenta"],
            "role" => $user["Rol"],
            "exp" => time() + (60 * 30) // 30 minutos de expiración
        ];
    
        $jwt = $this->generateJWT($payload);
    
        http_response_code(200);
        echo json_encode(["message" => "Inicio de sesión exitoso", "token" => $jwt]);
    }
    
    // Verifica si las credenciales son correctas
    private function authenticateUser($NumeroCuenta, $Pass) {
        $sql = "SELECT UsuarioID, NumeroCuenta, Pass, Rol FROM usuario WHERE NumeroCuenta = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $NumeroCuenta);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        // Validar la contraseña con password_verify()
        if ($result && password_verify($Pass, $result["Pass"])) {
            return $result;
        }
        return null;
    }

    // Generar JWT de forma segura
    private function generateJWT($payload) {
        $header = base64_encode(json_encode(["alg" => "HS256", "typ" => "JWT"]));
        $payload = base64_encode(json_encode($payload));
        $signature = hash_hmac("sha256", "$header.$payload", $this->secret_key, true);
        $signature = base64_encode($signature);

        return "$header.$payload.$signature";
    }
}
?>
