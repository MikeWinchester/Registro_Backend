<?php
require_once __DIR__ . '/BaseModel.php';

class Favorite extends BaseModel {
    public function __construct() {
        parent::__construct('tbl__libros_favoritos', 'usuario_id');
    }

    public function getFavoritosPorUsuario($usuarioUuid) {
        $query = "SELECT l.*, 
                 GROUP_CONCAT(DISTINCT a.nombre SEPARATOR '|') as autores,
                 GROUP_CONCAT(DISTINCT c.nombre SEPARATOR '|') as categorias
                 FROM tbl_libros_favoritos f
                 JOIN tbl_libro l ON f.libro_id = l.libro_id
                 JOIN tbl_usuario u ON f.usuario_id = u.usuario_id
                 LEFT JOIN tbl_libro_x_autor lxa ON l.libro_id = lxa.libro_id
                 LEFT JOIN tbl_autor a ON lxa.autor_id = a.autor_id
                 LEFT JOIN tbl_libro_x_categorias lxc ON l.libro_id = lxc.libro_id
                 LEFT JOIN tbl_categoria c ON lxc.categoria_id = c.categoria_id
                 WHERE u.id = ?
                 GROUP BY l.libro_id";
        
        $libros = $this->fetchAll($query, [$usuarioUuid]);
        
        foreach ($libros as &$libro) {
            $libro['autores'] = $libro['autores'] ? explode('|', $libro['autores']) : [];
            $libro['categorias'] = $libro['categorias'] ? explode('|', $libro['categorias']) : [];
        }
        
        return $libros;
    }

    public function agregarFavorito($usuarioId, $libroId) {
        $query = "INSERT INTO tbl_libros_favoritos (usuario_id, libro_id) 
                  SELECT u.usuario_id, l.libro_id 
                  FROM tbl_usuario u, tbl_libro l 
                  WHERE u.id = ? AND l.id = ?";
        
        return $this->executeWrite($query, [$usuarioId, $libroId]);
    }

    public function eliminarFavorito($usuarioId, $libroId) {
        $query = "DELETE f FROM tbl_libros_favoritos f
                  JOIN tbl_usuario u ON f.usuario_id = u.usuario_id
                  JOIN tbl_libro l ON f.libro_id = l.libro_id
                  WHERE u.id = ? AND l.id = ?";
        
        return $this->executeWrite($query, [$usuarioId, $libroId]);
    }

    public function esFavorito($usuarioId, $libroId) {
        $query = "SELECT COUNT(*) as count FROM tbl_libros_favoritos f
                  JOIN tbl_usuario u ON f.usuario_id = u.usuario_id
                  JOIN tbl_libro l ON f.libro_id = l.libro_id
                  WHERE u.id = ? AND l.id = ?";
        
        $result = $this->fetchOne($query, [$usuarioId, $libroId]);
        return $result['count'] > 0;
    }
}