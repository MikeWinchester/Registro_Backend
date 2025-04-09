<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Role.php';

class RoleController extends BaseController {
    private $roleModel;
    
    public function __construct() {
        parent::__construct();
        $this->roleModel = new Role();
    }
    
    public function getAll($request) {
        $roles = $this->roleModel->getAll();
        $this->jsonResponse($roles);
    }
    
    public function getById($request) {
        $roleId = $request->getRouteParam(0);
        $role = $this->roleModel->getById($roleId);
        
        if (!$role) {
            $this->jsonResponse(['error' => 'Role not found'], 404);
            return;
        }
        
        $this->jsonResponse($role);
    }
    
    public function create($request) {
        $data = $request->getBody();
        $this->validateRequiredFields($data, ['name']);
        
        if ($this->roleModel->getByName($data['name'])) {
            $this->jsonResponse(['error' => 'Role already exists'], 400);
            return;
        }
        
        $roleId = $this->roleModel->create($data);
        
        $this->jsonResponse([
            'message' => 'Role created successfully',
            'role_id' => $roleId
        ], 201);
    }
    
    public function update($request) {
        $roleId = $request->getRouteParam(0);
        $data = $request->getBody();
        
        $success = $this->roleModel->update($roleId, $data);
        
        if ($success) {
            $this->jsonResponse(['message' => 'Role updated successfully']);
        } else {
            $this->jsonResponse(['error' => 'Failed to update role'], 400);
        }
    }
    
    public function delete($request) {
        $roleId = $request->getRouteParam(0);
        $success = $this->roleModel->delete($roleId);
        
        if ($success) {
            $this->jsonResponse(['message' => 'Role deleted successfully']);
        } else {
            $this->jsonResponse(['error' => 'Failed to delete role'], 400);
        }
    }
}