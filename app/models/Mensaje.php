<?php
require_once __DIR__ . "/../core/Model.php";

class Mensaje extends Model {
    protected $table = 'tbl_mensajes';
    protected $primaryKey = 'mensaje_id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}
?>