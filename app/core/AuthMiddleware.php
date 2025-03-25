<?php
class AuthMiddleware {
    private static $secret_key;

    public static function init() {
        $config = $GLOBALS['config'];
        self::$secret_key = $config["SECRET_KEY"];
    }

    public static function authMiddleware() {
        self::init();

        $headers = getallheaders();
        if (!isset($headers["Authorization"])) {
            http_response_code(401);
            echo json_encode(["error" => "Token requerido"]);
            exit();
        }

        $token = str_replace("Bearer ", "", $headers["Authorization"]);
        $user = self::verifyJWT($token);

        if (!$user) {
            http_response_code(401);
            echo json_encode(["error" => "Token inválido o expirado"]);
            exit();
        }

        return $user;
    }

    public static function checkRoleWithAuth($required_roles) {
        $user = self::authMiddleware();

        if (!array_intersect($user["Roles"], $required_roles)) {
            http_response_code(403);
            echo json_encode(["error" => "Acceso denegado, rol insuficiente"]);
            exit();
        }

        return true;
    }

    public static function verifyJWT($token) {
        self::init();
        
        $parts = explode(".", $token);
        if (count($parts) !== 3) return null;

        list($header, $payload, $signature) = $parts;
        $valid_signature = base64_encode(hash_hmac("sha256", "$header.$payload", self::$secret_key, true));

        if ($valid_signature !== $signature) return null;

        $decoded_payload = json_decode(base64_decode($payload), true);
        if (!$decoded_payload) return null;
        if (!isset($decoded_payload["Roles"]) || !is_array($decoded_payload["Roles"])) return null;
        if ($decoded_payload["exp"] < time()) return null;

        return $decoded_payload;
    }
}
?>
