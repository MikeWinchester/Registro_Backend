<?php
require_once __DIR__ . '/BaseModel.php';

class Tag extends BaseModel {
    public function __construct() {
        parent::__construct('tbl_categoria', 'categoria_id');
    }

    public function getAllCategorias() {
        $query = "SELECT * FROM tbl_categoria ORDER BY nombre";
        return $this->fetchAll($query);
    }

    public function crearSiNoExiste($nombre) {
        $categoriaExistente = $this->fetchOne(
            "SELECT * FROM {$this->table} WHERE nombre = ?", 
            [$nombre]
        );
        
        if ($categoriaExistente) {
            return $categoriaExistente['categoria_id'];
        }
        
        return $this->create(['nombre' => $nombre]);
    }
}