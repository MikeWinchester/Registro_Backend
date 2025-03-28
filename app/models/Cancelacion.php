<?php
require_once __DIR__ . "/../core/Model.php";

class Cancelacion extends Model {
    protected $table = 'tbl_lista_cancelacion';
    protected $primaryKey = 'lista_cancelacion_id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}
?>