<?php
require_once __DIR__ . '/../core/Database.php';

class BaseController {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    
    protected function validateRequiredFields($data, $requiredFields) {
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            $this->jsonResponse([
                'error' => 'Missing required fields',
                'missing_fields' => $missingFields
            ], 400);
            exit;
        }
    }
    
    protected function handleException($exception) {
        if (APP_DEBUG) {
            $this->jsonResponse([
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace()
            ], 500);
        } else {
            $this->jsonResponse([
                'error' => 'Internal Server Error'
            ], 500);
        }
    }
}