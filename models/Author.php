<?php
require_once __DIR__ . '/BaseModel.php';

class Author extends BaseModel {
    public function __construct() {
        parent::__construct('tbl_autor', 'autor_id');
    }

    public function crearSiNoExiste($nombre) {
        $autorExistente = $this->fetchOne(
            "SELECT * FROM {$this->table} WHERE nombre = ?", 
            [$nombre]
        );
        
        if ($autorExistente) {
            return $autorExistente['autor_id'];
        }
        
        return $this->create(['nombre' => $nombre]);
    }
}
?>