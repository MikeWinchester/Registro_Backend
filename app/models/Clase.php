<?php
require_once __DIR__ . "/../core/Model.php";

class Clase extends Model {
    protected $table = 'tbl_clase';
    protected $primaryKey = 'clase_id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}
?>