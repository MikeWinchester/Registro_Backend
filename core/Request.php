<?php
class Request {
    private $routeParams = [];
    private $user = null;
    
    public static function getMethod() {
        $headers = self::getHeaders();
        
        // Verificar override
        if (isset($headers['X-Http-Method-Override'])) {
            return self::validateMethodOverride($headers['X-Http-Method-Override']);
        }
        
        return strtoupper($_SERVER['REQUEST_METHOD']);
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
        $method = self::getMethod();
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $isFormData = strpos($contentType, 'multipart/form-data') !== false;
        
        // Caso especial: PUT/PATCH con FormData
        if (($method === 'PUT' || $method === 'PATCH') && $isFormData) {
            $body = self::parseFormData();
        } 
        // GET parameters
        elseif ($method === 'GET') {
            foreach ($_GET as $key => $value) {
                $body[$key] = self::sanitizeInput($value);
            }
        }
        // JSON input
        elseif (strpos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true);
            if (is_array($input)) {
                $body = array_map([self::class, 'sanitizeInput'], $input);
            }
        }
        // POST/PUT/PATCH standard
        else {
            $inputSource = ($method === 'POST') ? $_POST : [];
            if (empty($inputSource)) {
                parse_str(file_get_contents('php://input'), $inputSource);
            }
            
            foreach ($inputSource as $key => $value) {
                $body[$key] = self::sanitizeInput($value);
            }
        }
        
        return $body;
    }

    private static function parseFormData() {
        $body = [];
        $rawData = file_get_contents('php://input');
        $boundary = substr($rawData, 0, strpos($rawData, "\r\n"));
        
        if (!$boundary) return $body;
        
        $parts = array_slice(explode($boundary, $rawData), 1);
        
        foreach ($parts as $part) {
            if ($part === "--\r\n") continue;
            
            $part = ltrim($part, "\r\n");
            list($rawHeaders, $content) = explode("\r\n\r\n", $part, 2);
            $content = substr($content, 0, strlen($content) - 2); // Remove trailing \r\n
            
            $headers = [];
            foreach (explode("\r\n", $rawHeaders) as $header) {
                list($name, $value) = explode(':', $header, 2);
                $headers[strtolower(trim($name))] = trim($value);
            }
            
            if (isset($headers['content-disposition'])) {
                preg_match('/name="([^"]+)"/', $headers['content-disposition'], $matches);
                $fieldName = $matches[1] ?? '';
                
                if ($fieldName && !isset($headers['content-type'])) {
                    $body[$fieldName] = self::sanitizeInput($content);
                    
                    // Manejar arrays (campo[])
                    if (strpos($fieldName, '[]') !== false) {
                        $cleanName = str_replace('[]', '', $fieldName);
                        if (!isset($body[$cleanName])) {
                            $body[$cleanName] = [];
                        }
                        $body[$cleanName][] = self::sanitizeInput($content);
                    }
                }
            }
        }
        
        return $body;
    }
    
    private static function validateMethodOverride($method) {
        $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];
        
        // Validar que el método override sea permitido
        if (!in_array(strtoupper($method), $allowedMethods)) {
            throw new InvalidArgumentException("Método HTTP no permitido: $method");
        }
        
        return strtoupper($method);
    }

    private static function sanitizeInput($value) {
        if (is_array($value)) {
            return array_map([self::class, 'sanitizeInput'], $value);
        }
        return filter_var(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'), FILTER_SANITIZE_SPECIAL_CHARS);
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

    public function hasFile($name) {
        return isset($_FILES[$name]) && $_FILES[$name]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    public function getFile($name) {
        return $_FILES[$name] ?? null;
    }

    public function getUploadedFile($name) {
        if (!$this->hasFile($name)) {
            return null;
        }

        $file = $_FILES[$name];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error al subir el archivo: código {$file['error']}");
        }

        return $file;
    }
}