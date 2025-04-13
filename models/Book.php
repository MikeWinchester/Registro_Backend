<?php
require_once __DIR__ . '/BaseModel.php';

class Book extends BaseModel {
    public function __construct() {
        parent::__construct('tbl_libro', 'libro_id');
    }

    public function getLibrosConDetalles($filtros = [], $pagina = 1, $porPagina = 6) {
        $offset = ($pagina - 1) * $porPagina;
        
        $query = "SELECT l.*, 
                 GROUP_CONCAT(DISTINCT a.nombre SEPARATOR '|') as autores,
                 GROUP_CONCAT(DISTINCT c.nombre SEPARATOR '|') as categorias
                 FROM tbl_libro l
                 LEFT JOIN tbl_libro_x_autor lxa ON l.libro_id = lxa.libro_id
                 LEFT JOIN tbl_autor a ON lxa.autor_id = a.autor_id
                 LEFT JOIN tbl_libro_x_categorias lxc ON l.libro_id = lxc.libro_id
                 LEFT JOIN tbl_categoria c ON lxc.categoria_id = c.categoria_id";
        
        $where = [];
        $params = [];
        
        // Filtro de búsqueda
        if (!empty($filtros['busqueda'])) {
            $where[] = "(l.titulo LIKE ? OR a.nombre LIKE ?)";
            $searchTerm = "%{$filtros['busqueda']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Filtro de categorías modificado
        if (!empty($filtros['categorias'])) {
            $placeholders = implode(',', array_fill(0, count($filtros['categorias']), '?'));
            $where[] = "c.nombre IN ($placeholders)";
            $params = array_merge($params, $filtros['categorias']);
        }
        
        if (!empty($where)) {
            $query .= " WHERE " . implode(' AND ', $where);
        }
        
        $query .= " GROUP BY l.libro_id
                   ORDER BY l.titulo
                   LIMIT ? OFFSET ?";
        
        $params[] = $porPagina;
        $params[] = $offset;
        
        $libros = $this->fetchAll($query, $params);
        
        // Formatear resultados
        foreach ($libros as &$libro) {
            $libro['autores'] = $libro['autores'] ? explode('|', $libro['autores']) : [];
            $libro['categorias'] = $libro['categorias'] ? explode('|', $libro['categorias']) : [];
        }
        
        return $libros;
    }

    public function contarLibrosFiltrados($filtros = []) {
        $query = "SELECT COUNT(DISTINCT l.libro_id) as total
                 FROM tbl_libro l
                 LEFT JOIN tbl_libro_x_autor lxa ON l.libro_id = lxa.libro_id
                 LEFT JOIN tbl_autor a ON lxa.autor_id = a.autor_id
                 LEFT JOIN tbl_libro_x_categorias lxc ON l.libro_id = lxc.libro_id
                 LEFT JOIN tbl_categoria c ON lxc.categoria_id = c.categoria_id";
        
        $where = [];
        $params = [];
        
        // Filtros
        if (!empty($filtros['busqueda'])) {
            $where[] = "(l.titulo LIKE ? OR a.nombre LIKE ?)";
            $searchTerm = "%{$filtros['busqueda']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filtros['categorias'])) {
            $placeholders = implode(',', array_fill(0, count($filtros['categorias']), '?'));
            $where[] = "c.nombre IN ($placeholders)";
            $params = array_merge($params, $filtros['categorias']);
        }
        
        if (!empty($where)) {
            $query .= " WHERE " . implode(' AND ', $where);
        }
        
        $result = $this->fetchOne($query, $params);
        return $result['total'] ?? 0;
    }

    public function crearLibroConArchivos($datosLibro, $autores, $categorias) {
        $this->connection->begin_transaction();
        
        try {
            // Insertar libro
            $libroId = $this->create($datosLibro);
            
            // Asociar autores
            $this->asociarAutores($libroId, $autores);
            
            
            // Asociar categorías
            $this->asociarCategorias($libroId, $categorias);
            
            $this->connection->commit();
            return $libroId;

            
        } catch (Exception $e) {
            $this->connection->rollback();
            throw $e;
        }
    }

    public function actualizarLibroConArchivos($libroId, $datosLibro, $autores, $categorias) {
        $this->connection->begin_transaction();
        
        try {
            // Actualizar libro
            $this->update($libroId, $datosLibro);

            $book = $this->getById($libroId);
            
            // Actualizar autores
            $this->actualizarAutores($book['libro_id'], $autores);
            
            // Actualizar categorías
            $this->actualizarCategorias($book['libro_id'], $categorias);
            
            $this->connection->commit();
            return true;
            
        } catch (Exception $e) {
            $this->connection->rollback();
            throw $e;
        }
    }

    private function asociarAutores($libroId, $autores) {
        if (empty($autores)) return;
        
        $placeholders = implode(',', array_fill(0, count($autores), '(?,?)'));
        $values = [];
        foreach ($autores as $autorId) {
            $values[] = $libroId;
            $values[] = $autorId;
        }
        
        $query = "INSERT IGNORE INTO tbl_libro_x_autor (libro_id, autor_id) VALUES {$placeholders}";
        $this->executeWrite($query, $values);
    }

    private function actualizarAutores($libroId, $autores) {
        // Eliminar relaciones existentes
        $this->executeWrite(
            "DELETE FROM tbl_libro_x_autor WHERE libro_id = ?", 
            [$libroId]
        );
        
        // Crear nuevas relaciones
        $this->asociarAutores($libroId, $autores);
    }

    private function asociarCategorias($libroId, $categorias) {
        if (empty($categorias)) return;
        
        $placeholders = implode(',', array_fill(0, count($categorias), '(?,?)'));
        $values = [];
        foreach ($categorias as $categoriaId) {
            $values[] = $libroId;
            $values[] = $categoriaId;
        }
        
        $query = "INSERT IGNORE INTO tbl_libro_x_categorias (libro_id, categoria_id) VALUES {$placeholders}";
        $this->executeWrite($query, $values);
    }

    private function actualizarCategorias($libroId, $categorias) {
        // Eliminar relaciones existentes
        $this->executeWrite(
            "DELETE FROM tbl_libro_x_categorias WHERE libro_id = ?", 
            [$libroId]
        );
        
        // Crear nuevas relaciones
        $this->asociarCategorias($libroId, $categorias);
    }

    public function buscarAutorPorNombre($nombre) {
        $query = "SELECT * FROM tbl_autor WHERE nombre = ?";
        return $this->fetchOne($query, [$nombre]);
    }

    public function buscarCategoriaPorNombre($nombre) {
        $query = "SELECT * FROM tbl_categoria WHERE nombre = ?";
        return $this->fetchOne($query, [$nombre]);
    }
}