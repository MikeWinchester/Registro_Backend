<?php
require_once __DIR__ . "/../core/Model.php";

class Estudiante extends Model {
    protected $table = 'tbl_estudiante';
    protected $primaryKey = 'estudiante_id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}
?>
