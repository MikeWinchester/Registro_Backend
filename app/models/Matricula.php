<?php
require_once __DIR__ . "/../core/Model.php";

class Matricula extends Model {
    protected $table = 'tbl_matricula';
    protected $primaryKey = 'matricula_id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}

?>
