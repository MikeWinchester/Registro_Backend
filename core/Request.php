<?php
class Request {
    private $routeParams = [];
    private $user = null;
    
    public static function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    public static function getPath() {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        
        if ($position === false) {
            return $path;
        }
        
        return substr($path, 0, $position);
    }
    
    public static function getBody() {
        $body = [];
        
        if (self::getMethod() === 'GET') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        
        if (self::getMethod() === 'POST') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (is_array($input)) {
            $body = array_merge($body, $input);
        }
        
        return $body;
    }
    
    public static function getHeaders() {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))))] = $value;
            }
        }
        return $headers;
    }
    
    public function getRouteParam($index) {
        return $this->routeParams[$index] ?? null;
    }
    
    public function setRouteParams($params) {
        $this->routeParams = $params;
    }
    
    public function getUser() {
        return $this->user;
    }
    
    public function setUser($user) {
        $this->user = $user;
    }
    
    public function getQueryParam($key, $default = null) {
        return $_GET[$key] ?? $default;
    }
}