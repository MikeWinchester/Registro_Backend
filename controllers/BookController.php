<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Tag.php';
require_once __DIR__ . '/../models/Author.php';
require_once __DIR__ . '/../helpers/FileUploader.php';

class BookController extends BaseController {
    private $libroModel;
    private $categoriaModel;
    private $fileUploader;
    private $autorModel;

    public function __construct() {
        parent::__construct();
        $this->libroModel = new Book();
        $this->categoriaModel = new Tag();
        $this->fileUploader = new FileUploader();
        $this->autorModel = new Author();
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
            
            $filePath = __DIR__ . '/../data' . $archivo;
            
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
        
        if ($extension === 'pdf') {
            header('Content-Disposition: inline; filename="'.basename($path).'"');
        }
        
        readfile($path);
        exit;
    }
    
    private function servirArchivoPorDefecto($tipo) {
        $defaultFiles = [
            'pdf' => __DIR__ . '/../data/books/default.pdf',
            'portada' => __DIR__ . '/../data/books/default.jpg'
        ];
        
        header('Content-Type: ' . ($tipo === 'pdf' ? 'application/pdf' : 'image/jpeg'));
        readfile($defaultFiles[$tipo]);
        exit;
    }

    public function crearLibro($request) {
        try {
            $datos = $request->getBody();
            error_log(print_r($datos, true));

            
            $this->validateRequiredFields($datos, ['titulo']);

            // Procesar autores
            $autoresIds = $this->procesarAutores($datos['autores'] ?? []);

            // Procesar categorías
            $categoriasIds = $this->procesarCategorias($datos['categorias'] ?? []);

            // Datos básicos del libro
            $libroData = [
                'titulo' => $datos['titulo'],
                'descripcion' => $datos['descripcion'] ?? '',
            ];

            // Crear el libro en la base de datos
            $libroId = $this->libroModel->crearLibroConArchivos($libroData, $autoresIds, $categoriasIds);


            $libro = $this->libroModel->getByPrimaryKey($libroId);

            // Procesar archivos
            $this->procesarArchivos($libro, $request);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Libro creado exitosamente',
                'libro_id' => $libroId
            ]);

        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    public function actualizarLibro($request) {
        //try {
            $datos = $request->getBody();
            $libroId = $request->getRouteParam(0);
            $this->validateRequiredFields($datos, ['titulo']);

            error_log(print_r($datos, true));
            $libro = $this->libroModel->getById($libroId);
            
            if (!$libro) {
                $this->jsonResponse(['error' => 'Libro no encontrado'], 404);
                return;
            }

            // Procesar autores
            $autoresIds = $this->procesarAutores($datos['autores'] ?? []);
            error_log(print_r($autoresIds, true));

            // Procesar categorías
            $categoriasIds = $this->procesarCategorias($datos['categorias'] ?? []);
            error_log(print_r($categoriasIds, true));

            // Datos actualizados del libro
            $libroData = [
                'titulo' => $datos['titulo'],
                'descripcion' => $datos['descripcion'] ?? $libro['descripcion'],
            ];

            // Actualizar el libro
            $this->libroModel->actualizarLibroConArchivos($libroId, $libroData, $autoresIds, $categoriasIds);

            // Procesar archivos si se enviaron
            $this->procesarArchivos($libro, $request, true);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Libro actualizado exitosamente'
            ]);

        //} catch (Exception $e) {
            //$this->handleException($e);
        //}
    }

    public function eliminarLibro($request) {
        try {
            $libroId = $request->getRouteParam(0);
            $libro = $this->libroModel->getById($libroId);
            
            if (!$libro) {
                $this->jsonResponse(['error' => 'Libro no encontrado'], 404);
                return;
            }

            // Eliminar archivos primero
            $this->fileUploader->eliminarArchivosLibro($libro['id']);

            // Eliminar el libro de la base de datos
            $this->libroModel->delete($libroId);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Libro eliminado exitosamente'
            ]);

        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    // Métodos auxiliares
    private function procesarAutores($autoresNombres) {

        $autoresIds = [];
        foreach ($autoresNombres as $nombre) {
            $autoresIds[] = $this->autorModel->crearSiNoExiste($nombre);
        }
        return $autoresIds;
    }

    private function procesarCategorias($categoriasNombres) {

        $categoriasIds = [];
        foreach ($categoriasNombres as $nombre) {
            $categoriasIds[] = $this->categoriaModel->crearSiNoExiste($nombre);
        }
        return $categoriasIds;
    }

    private function procesarArchivos($libro, $request, $esActualizacion = false) {
        $uuid = $libro['id'];
        $libroDir = __DIR__ . '/../data/books/' . $uuid;

        // Procesar PDF
        if ($request->hasFile('archivo_pdf')) {
            $this->fileUploader->subirArchivo(
                $request->getFile('archivo_pdf'),
                "$libroDir/documento.pdf",
                ['application/pdf'],
                $esActualizacion
            );
            
            $this->libroModel->update($uuid, [
                'ruta_archivo' => "/books/$uuid/documento.pdf"
            ]);
        }

        // Procesar portada
        if ($request->hasFile('portada')) {
            $this->fileUploader->subirArchivo(
                $request->getFile('portada'),
                "$libroDir/portada.jpg",
                ['image/jpeg', 'image/png'],
                $esActualizacion
            );
            
            $this->libroModel->update($uuid, [
                'portada' => "/books/$uuid/portada.jpg"
            ]);
        }
    }
}