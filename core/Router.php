<?php
require_once __DIR__ . '/Request.php';
require_once __DIR__ . '/AuthMiddleware.php';

class Router {
    private $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => []
    ];
    
    private $protectedRoutes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => []
    ];
    
    private $request;
    
    public function __construct() {
        $this->request = new Request();
    }
    
    public function get($path, $callback, $allowedRoles = []) {
        $this->routes['GET'][$path] = $callback;
        if (!empty($allowedRoles)) {
            $this->protectedRoutes['GET'][$path] = $allowedRoles;
        }
    }
    
    public function post($path, $callback, $allowedRoles = []) {
        $this->routes['POST'][$path] = $callback;
        if (!empty($allowedRoles)) {
            $this->protectedRoutes['POST'][$path] = $allowedRoles;
        }
    }
    
    public function put($path, $callback, $allowedRoles = []) {
        $this->routes['PUT'][$path] = $callback;
        if (!empty($allowedRoles)) {
            $this->protectedRoutes['PUT'][$path] = $allowedRoles;
        }
    }
    
    public function delete($path, $callback, $allowedRoles = []) {
        $this->routes['DELETE'][$path] = $callback;
        if (!empty($allowedRoles)) {
            $this->protectedRoutes['DELETE'][$path] = $allowedRoles;
        }
    }
    
    public function resolve() {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        
        // Buscar coincidencia con parámetros
        if ($callback === false) {
            foreach ($this->routes[$method] as $route => $handler) {
                $pattern = "#^" . preg_replace('/\{[a-z]+\}/', '([^/]+)', $route) . "$#";
                if (preg_match($pattern, $path, $matches)) {
                    $callback = $handler;
                    array_shift($matches);
                    $this->request->setRouteParams($matches);
                    break;
                }
            }
        }
        
        if ($callback === false) {
            $this->sendResponse(['error' => 'Not Found'], 404);
            return;
        }
        
        // Verificar si la ruta está protegida
        if (isset($this->protectedRoutes[$method][$path])) {
            $allowedRoles = $this->protectedRoutes[$method][$path];
            try {
                $user = AuthMiddleware::handle($this->request, $allowedRoles);
                $this->request->setUser($user);
            } catch (Exception $e) {
                $this->sendResponse(['error' => $e->getMessage()], $e->getCode());
                return;
            }
        }
        
        if (is_array($callback)) {
            $controller = new $callback[0]();
            $method = $callback[1];
            
            if (method_exists($controller, $method)) {
                return call_user_func_array([$controller, $method], [$this->request]);
            }
        }
        
        $this->sendResponse(['error' => 'Not Found'], 404);
    }
    
    private function sendResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}