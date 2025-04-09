<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Tag.php';

class BookController extends BaseController {
    private $libroModel;
    private $categoriaModel;

    public function __construct() {
        parent::__construct();
        $this->libroModel = new Book();
        $this->categoriaModel = new Tag();
    }

    public function listarLibros($request) {
        try {
            $filtros = [
                'busqueda' => $request->getQueryParam('busqueda', ''),
                // Modificado para recibir array de categorías
                'categorias' => $request->getQueryParam('categorias', []) ?: []
            ];
            
            // Asegurar que porPagina sea 6
            $pagina = max(1, (int)$request->getQueryParam('pagina', 1));
            $porPagina = 6; // Fijo en 6 como solicitaste
            
            $libros = $this->libroModel->getLibrosConDetalles($filtros, $pagina, $porPagina);
            $total = $this->libroModel->contarLibrosFiltrados($filtros);
            $categorias = $this->categoriaModel->getAllCategorias();
            
            $this->jsonResponse([
                'libros' => $libros,
                'total' => $total,
                'pagina_actual' => $pagina,
                'total_paginas' => max(1, ceil($total / $porPagina)),
                'por_pagina' => $porPagina,
                'categorias' => $categorias
            ]);
            
        } catch (Exception $e) {
            $this->jsonResponse([
                'error' => 'Error al obtener libros',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function obtenerLibro($request) {
        try {
            $uuid = $request->getRouteParam(0);
            $libro = $this->libroModel->getById($uuid);
            
            if (!$libro) {
                $this->jsonResponse(['error' => 'Libro no encontrado'], 404);
                return;
            }
            
            $this->jsonResponse($libro);
            
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    public function servirArchivoLibro($request) {
        try {
            $uuid = $request->getRouteParam(0);
            $tipoArchivo = $request->getRouteParam(1);
            $libro = $this->libroModel->getById($uuid);
            
            if (!$libro) {
            $this->servirArchivoPorDefecto($tipoArchivo);
            }
            
            // Determinar qué archivo servir
            $archivo = match($tipoArchivo) {
                'pdf' => $libro['ruta_archivo'],
                'portada' => $libro['portada'],
                default => throw new Exception('Tipo de archivo no válido')
            };
            
            $filePath = __DIR__ . '/../uploads' . $archivo;

            error_log($filePath);
            
            if (!file_exists($filePath)) {
                return $this->servirArchivoPorDefecto($tipoArchivo);
            }
            
            $this->servirArchivo($filePath);
            
        } catch (Exception $e) {
            $this->servirArchivoPorDefecto($tipoArchivo);
        }
    }
    
    private function servirArchivo($path) {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png'
        ];
        
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $mimeType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
        
        header('Content-Type: ' . $mimeType);
        
        // Para PDFs: mostrar en el navegador (no descargar)
        if ($extension === 'pdf') {
            header('Content-Disposition: inline; filename="'.basename($path).'"');
        }
        
        readfile($path);
        exit;
    }
    
    private function servirArchivoPorDefecto($tipo) {
        $defaultFiles = [
            'pdf' => __DIR__ . '/../uploads/libros/default.pdf',
            'portada' => __DIR__ . '/../uploads/libros/default.jpg'
        ];
        
        header('Content-Type: ' . ($tipo === 'pdf' ? 'application/pdf' : 'image/jpeg'));
        readfile($defaultFiles[$tipo]);
        exit;
    }
    
}