<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Saved.php';

class SavedController extends BaseController {
    private $guardadoModel;

    public function __construct() {
        parent::__construct();
        $this->guardadoModel = new Saved();
    }

    public function listarGuardados($request) {
        try {
            $userId = $request->getQueryParam('user_id');
            
            if (!$userId) {
                $this->jsonResponse(['error' => 'Usuario no autenticado'], 401);
                return;
            }
            
            $guardados = $this->guardadoModel->getGuardadosPorUsuario($userId);
            
            $this->jsonResponse($guardados);
            
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    public function toggleGuardado($request) {
        try {
            $userId = $request->getQueryParam('user_id');
            
            if (!$userId) {
                $this->jsonResponse(['error' => 'Usuario no autenticado'], 401);
                return;
            }
            
            $data = $request->getBody();
            
            $this->validateRequiredFields($data, ['libroId']);
            
            $libroId = $data['libroId'];
            
            if ($this->guardadoModel->esGuardado($userId, $libroId)) {
                $this->guardadoModel->eliminarGuardado($userId, $libroId);
                $accion = 'eliminado';
            } else {
                $this->guardadoModel->agregarGuardado($userId, $libroId);
                $accion = 'agregado';
            }
            
            $this->jsonResponse([
                'mensaje' => "Libro {$accion} a guardados",
                'esGuardado' => $this->guardadoModel->esGuardado($userId, $libroId)
            ]);
            
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }
}