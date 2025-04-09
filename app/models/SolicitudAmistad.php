<?php
require_once __DIR__ . "/../core/Model.php";

class SolicitudAmistad extends Model {
    protected $table = 'tbl_solicitud';
    protected $primaryKey = ['usuario_emisor', 'usuario_destino'];
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}

?>