<?php
require_once __DIR__ . '/../controllers/TagController.php';

function registerTagRoutes($router) {
    $tagController = new TagController();
    
    $router->get('/tags', [$tagController, 'getAll']);
}
?>