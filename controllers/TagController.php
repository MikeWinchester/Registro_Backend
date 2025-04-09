<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Tag.php';

class TagController extends BaseController {
    private $tagModel;

    public function __construct() {
        parent::__construct();
        $this->tagModel = new Tag();
    }
    
    public function getAll($request) {
        
        $tags= $this->tagModel->getAllCategorias();
        
        $this->jsonResponse($tags);
    }
}