<?php
require_once __DIR__ . "/../core/Model.php";

class Espera extends Model {
    protected $table = 'tbl_lista_espera';
    protected $primaryKey = 'lista_espera_id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}
?>