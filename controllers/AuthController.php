<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Role.php';
require_once __DIR__ . '/../core/JwtAuth.php';

class AuthController extends BaseController {
    private $userModel;
    private $roleModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->roleModel = new Role();
    }
    
    public function login($request) {
        $data = $request->getBody();

        $this->validateRequiredFields($data, ['accountNumber', 'password']);
        
        $user = $this->userModel->getByAccountNumber($data['accountNumber']);
        
        if (!$user || !password_verify($data['password'], $user['contrasenia'])) {
            $this->jsonResponse(['error' => 'Credenciales inválidas'], 401);
            return;
        }

        $roles = $this->userModel->getUserRoles($user['id']);
        
        if (empty($roles)) {
            $this->jsonResponse(['error' => 'User has no roles assigned'], 403);
            return;
        }
        
        $token = JwtAuth::generateToken($user['id'], $roles);
        
        $this->jsonResponse([
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'name' => $user['nombre_completo'],
                'accountNumber' => $user['numero_cuenta'],
                'roles' => $roles
            ]
        ]);
    }
    
    public function register($request) {
        $data = $request->getBody();
        $this->validateRequiredFields($data, ['name', 'accountNumber', 'password']);
        
        if ($this->userModel->getByAccountNumber($data['accountNumber'])) {
            $this->jsonResponse(['error' => 'Email already in use'], 400);
            return;
        }
        
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $userId = $this->userModel->create($data);
        
        // Asignar rol por defecto (student) si no se especifican roles
        if (empty($data['roles'])) {
            $defaultRole = $this->roleModel->getByName('Estudiante');
            if ($defaultRole) {
                $this->userModel->addRoleToUser($userId, $defaultRole['id']);
            }
        } elseif (is_array($data['roles'])) {
            foreach ($data['roles'] as $roleName) {
                $role = $this->roleModel->getByName($roleName);
                if ($role) {
                    $this->userModel->addRoleToUser($userId, $role['id']);
                }
            }
        }
        
        $this->jsonResponse([
            'message' => 'User registered successfully',
            'user_id' => $userId
        ], 201);
    }
    
    public function me($request) {
        $user = $request->getUser();
        
        $this->jsonResponse([
            'id' => $user['user_id'],
            'name' => $user['details']['nombre_completo'],
            'accountNumber' => $user['details']['numero_cuenta'],
            'roles' => $user['roles'],
        ]);
    }
}