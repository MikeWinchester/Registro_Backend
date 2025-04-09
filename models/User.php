<?php
require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    public function __construct() {
        parent::__construct('tbl_usuario', 'usuario_id');
    }
    
    public function getByAccountNumber(string $accountNumber): ?array {
        return $this->fetchOne(
            "SELECT * FROM {$this->table} WHERE numero_cuenta = ? LIMIT 1", 
            [$accountNumber]
        );
    }
    
    public function getUserRoles(string $userId): array {
        $user = $this->getById($userId);
        if (!$user) return [];
        
        return $this->fetchAll(
            "SELECT r.nombre_rol, r.id
             FROM tbl_usuario_x_rol ur
             JOIN tbl_rol r ON ur.rol_id = r.rol_id
             JOIN tbl_usuario u ON ur.usuario_id = u.usuario_id
             WHERE u.id = ?",
            [$userId]
        );
    }
    
    public function addRoleToUser(string $userId, string $roleUuid): bool {
        $user = $this->getById($userId);
        $roleModel = new Role();
        $role = $roleModel->getById($roleUuid);
        
        if (!$user || !$role) return false;
        
        return $this->executeWrite(
            "INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (?, ?)",
            [$user['usuario_id'], $role['rol_id']]
        ) > 0;
    }
    
    public function removeRoleFromUser(string $userId, string $roleUuid): bool {
        $user = $this->getById($userId);
        $roleModel = new Role();
        $role = $roleModel->getById($roleUuid);
        
        if (!$user || !$role) return false;
        
        return $this->executeWrite(
            "DELETE FROM tbl_usuario_x_rol WHERE usuario_id = ? AND rol_id = ?",
            [$user['usuario_id'], $role['rol_id']]
        ) > 0;
    }
    
    public function updatePassword(string $userId, string $hashedPassword): bool {
        return $this->executeWrite(
            "UPDATE {$this->table} SET contrasenia = ? WHERE id = ?",
            [$hashedPassword, $userId]
        ) > 0;
    }
}