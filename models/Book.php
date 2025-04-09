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
}