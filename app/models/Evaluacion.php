<?php
require_once __DIR__ . "/../core/Model.php";

class Evaluacion extends Model {
    protected $table = 'tbl_evaluacion';
    protected $primaryKey = ['estudiante_id', 'docente_id'];
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}

?>