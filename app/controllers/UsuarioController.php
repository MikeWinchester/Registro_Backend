<?php
require_once __DIR__ . "/../models/Usuario.php";
require_once __DIR__ . "/../core/AuthMiddleware.php";
require_once __DIR__ . "/../core/Cors.php";


class UsuarioController {
    private $user;

    public function __construct() {
        $this->user = new Usuario();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    public function test() {
        AuthMiddleware::authMiddleware(); // Solo usuarios autenticados pueden ver la lista
        echo json_encode(["message" => "Lista de usuarios", "data" => $this->user->getAll()]);
    }

    // Obtener perfil del usuario autenticado
    public function getProfile() {
        $user = AuthMiddleware::authMiddleware(); // Verifica JWT
        http_response_code(200);
        echo json_encode(["message" => "Acceso concedido", "data" => $user]);
    }

    // Obtener todos los usuarios
    public function getAllUsers() {
        AuthMiddleware::authMiddleware(); // Solo usuarios autenticados pueden ver la lista
        echo json_encode(["message" => "Lista de usuarios", "data" => $this->user->getAll()]);
    }

    // Obtener un usuario por ID
    public function getOneUser($id) {
        AuthMiddleware::authMiddleware();
        $result = $this->user->getOne($id);
        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Usuario encontrado", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Usuario no encontrado"]);
        }
    }

    public function createUser() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data["NombreCompleto"]) || !isset($data["Identidad"]) || !isset($data["CorreoPersonal"]) ||
            !isset($data["Pass"]) || !isset($data["Rol"]) || !isset($data["NumeroCuenta"])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos requeridos"]);
            return;
        }

        // Hashear la contraseña
        $data["Pass"] = password_hash($data["Pass"], PASSWORD_DEFAULT);

        if ($this->user->create($data)) {
            echo json_encode(["message" => "Usuario creado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear usuario"]);
        }
    }

    // Actualizar un usuario por ID
    public function updateUser($id) {
        AuthMiddleware::authMiddleware();
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data)) {
            http_response_code(400);
            echo json_encode(["error" => "Datos inválidos"]);
            return;
        }

        if (isset($data["Pass"])) {
            $data["Pass"] = password_hash($data["Pass"], PASSWORD_DEFAULT);
        }

        if ($this->user->update($id, $data)) {
            http_response_code(200);
            echo json_encode(["message" => "Usuario actualizado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar usuario"]);
        }
    }

    // Eliminar un usuario por ID
    public function deleteUser($id) {
        AuthMiddleware::authMiddleware();
        if ($this->user->delete($id)) {
            http_response_code(200);
            echo json_encode(["message" => "Usuario eliminado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar usuario"]);
        }
    }

    // Contar usuarios
    public function countUsers() {
        AuthMiddleware::authMiddleware();
        $total = $this->user->count();
        http_response_code(200);
        echo json_encode(["message" => "Total de usuarios obtenidos", "data" => ["totalUsuarios" => $total]]);
    }
}
?>
