<?php
require_once __DIR__ . "/../core/Model.php";

class Departamentos extends Model {
    protected $table = 'tbl_departamento';
    protected $primaryKey = 'departamento_id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}
?>