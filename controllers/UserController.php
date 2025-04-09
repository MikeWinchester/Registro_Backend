<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Role.php';

class UserController extends BaseController {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }
    
    public function getAll($request) {
        $page = $request->getQueryParam('page', 1);
        $perPage = $request->getQueryParam('per_page', DEFAULT_PER_PAGE);
        
        $users = $this->userModel->getAll($page, $perPage);
        
        foreach($users as &$user){
            unset($user['contrasenia'], $user['usuario_id']);
        }
        
        $this->jsonResponse($users);
    }
    
    public function getById($request) {
        $userUuid = $request->getRouteParam(0);
        $user = $this->userModel->getById($userUuid);
        
        if (!$user) {
            $this->jsonResponse(['error' => 'User not found'], 404);
            return;
        }
        
        unset($user['contrasenia'], $user['usuario_id']);
        $user['roles'] = $this->userModel->getUserRoles($userUuid);
        
        $this->jsonResponse($user);
    }
    
    public function create($request) {
        $data = $request->getBody();
        $this->validateRequiredFields($data, ['nombre_completo', 'numero_cuenta', 'contrasenia']);
        
        if ($this->userModel->getByAccountNumber($data['numero_cuenta'])) {
            $this->jsonResponse(['error' => 'Account number already in use'], 400);
            return;
        }
        
        $data['contrasenia'] = password_hash($data['contrasenia'], PASSWORD_BCRYPT);
        $userId = $this->userModel->create($data);
        $user = $this->userModel->getById($userId);
        
        $this->jsonResponse([
            'message' => 'User created successfully',
            'user_id' => $user['id']
        ], 201);
    }
    
    public function update($request) {
        $userUuid = $request->getRouteParam(0);
        $data = $request->getBody();
        
        if (isset($data['contrasenia'])) {
            $data['contrasenia'] = password_hash($data['contrasenia'], PASSWORD_BCRYPT);
        }
        
        $user = $this->userModel->getById($userUuid);
        if (!$user) {
            $this->jsonResponse(['error' => 'User not found'], 404);
            return;
        }
        
        $success = $this->userModel->update($user['usuario_id'], $data);
        
        $this->jsonResponse([
            'success' => $success,
            'message' => $success ? 'User updated successfully' : 'Failed to update user'
        ], $success ? 200 : 400);
    }
    
    public function delete($request) {
        $userUuid = $request->getRouteParam(0);
        $user = $this->userModel->getById($userUuid);
        
        if (!$user) {
            $this->jsonResponse(['error' => 'User not found'], 404);
            return;
        }
        
        $success = $this->userModel->delete($user['usuario_id']);
        
        $this->jsonResponse([
            'success' => $success,
            'message' => $success ? 'User deleted successfully' : 'Failed to delete user'
        ], $success ? 200 : 400);
    }
    
    public function getUserRoles($request) {
        $userUuid = $request->getRouteParam(0);
        $roles = $this->userModel->getUserRoles($userUuid);
        
        $this->jsonResponse($roles);
    }
    
    public function assignRole($request) {
        $userUuid = $request->getRouteParam(0);
        $data = $request->getBody();
        
        $this->validateRequiredFields($data, ['rol_id']);
        
        $success = $this->userModel->addRoleToUser($userUuid, $data['rol_id']);
        
        $this->jsonResponse([
            'success' => $success,
            'message' => $success ? 'Role assigned successfully' : 'Failed to assign role'
        ], $success ? 200 : 400);
    }
    
    public function removeRole($request) {
        $userUuid = $request->getRouteParam(0);
        $roleUuid = $request->getRouteParam(1);
        
        $success = $this->userModel->removeRoleFromUser($userUuid, $roleUuid);
        
        $this->jsonResponse([
            'success' => $success,
            'message' => $success ? 'Role removed successfully' : 'Failed to remove role'
        ], $success ? 200 : 400);
    }
    
    public function updatePassword($request) {
        $userUuid = $request->getRouteParam(0);
        $data = $request->getBody();
        
        $this->validateRequiredFields($data, ['current_password', 'new_password']);
        
        $user = $this->userModel->getById($userUuid);
        if (!$user) {
            $this->jsonResponse(['error' => 'User not found'], 404);
            return;
        }
        
        if (!password_verify($data['current_password'], $user['contrasenia'])) {
            $this->jsonResponse(['error' => 'Current password is incorrect'], 400);
            return;
        }
        
        $success = $this->userModel->updatePassword(
            $userUuid, 
            password_hash($data['new_password'], PASSWORD_BCRYPT)
        );
        
        $this->jsonResponse([
            'success' => $success,
            'message' => $success ? 'Password updated successfully' : 'Failed to update password'
        ], $success ? 200 : 400);
    }
}