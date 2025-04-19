<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Center.php';
require_once __DIR__ . '/../models/Career.php';

class CenterController extends BaseController {
    private $centroModel;
    private $carreraModel;

    public function __construct() {
        parent::__construct();
        $this->centroModel = new Center();
        $this->carreraModel = new Career();
    }

    public function getAll($request) {
        try {
            $centros = $this->centroModel->getAll();
            $this->jsonResponse([
                'success' => true,
                'data' => $centros
            ]);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    public function getCarrerasByCentro($request) {
        try {
            $codigoCentro = $request->getRouteParam(0);
            
            if (empty($codigoCentro)) {
                $this->jsonResponse(['error' => 'Código de centro requerido'], 400);
                return;
            }

            $centro = $this->centroModel->getByCode($codigoCentro);
            if (!$centro) {
                $this->jsonResponse(['error' => 'Centro regional no encontrado'], 404);
                return;
            }

            $carreras = $this->carreraModel->getByCentroRegional($centro['centro_regional_id']);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $carreras
            ]);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }
}