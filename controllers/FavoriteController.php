<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Favorite.php';

class FavoriteController extends BaseController {
    private $favoritoModel;

    public function __construct() {
        parent::__construct();
        $this->favoritoModel = new Favorite();
    }

    public function listarFavoritos($request) {
        try {
            $userId = $request->getQueryParam('user_id');
            
            if (!$userId) {
                $this->jsonResponse(['error' => 'Usuario no autenticado'], 401);
                return;
            }
            
            $favoritos = $this->favoritoModel->getFavoritosPorUsuario($userId);
            
            $this->jsonResponse($favoritos);
            
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    public function toggleFavorito($request) {
        try {
            $userId = $request->getQueryParam('user_id');
            
            if (!$userId) {
                $this->jsonResponse(['error' => 'Usuario no autenticado'], 401);
                return;
            }
            
            $data = $request->getBody();
            
            $this->validateRequiredFields($data, ['libroId']);
            
            $libroId = $data['libroId'];
            
            if ($this->favoritoModel->esFavorito($userId, $libroId)) {
                $this->favoritoModel->eliminarFavorito($userId, $libroId);
                $accion = 'eliminado';
            } else {
                $this->favoritoModel->agregarFavorito($userId, $libroId);
                $accion = 'agregado';
            }
            
            $this->jsonResponse([
                'mensaje' => "Libro {$accion} a favoritos",
                'esFavorito' => $this->favoritoModel->esFavorito($userId, $libroId)
            ]);
            
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }
}