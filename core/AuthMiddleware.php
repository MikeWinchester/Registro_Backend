<?php
require_once __DIR__ . '/JwtAuth.php';
require_once __DIR__ . '/../models/User.php';

class AuthMiddleware {
    public static function handle($request, $allowedRoles = []) {
        $user = JwtAuth::getAuthUser();
        if ($user) {
            $request->setUser($user);
        }else{
            throw new Exception('Unauthorized - Token missing or invalid', 401);
        }
        
        if (!empty($allowedRoles)) {
            $hasRole = false;
            foreach ($user['roles'] as $role) {
                if (in_array($role, $allowedRoles)) {
                    $hasRole = true;
                    break;
                }
            }
            
            if (!$hasRole) {
                throw new Exception('Forbidden - Insufficient permissions', 403);
            }
        }
        
        // Obtener información completa del usuario
        $userModel = new User();
        $userDetails = $userModel->getById($user['user_id']);
        
        if (!$userDetails) {
            throw new Exception('User not found', 404);
        }
        
        // Combinar datos del token con datos de la base de datos
        $user['details'] = $userDetails;
        unset($user['details']['contrasenia']);
        
        return $user;
    }
}