<?php
require_once __DIR__ . "/../core/Model.php";

class Docente extends Model {
    protected $table = 'tbl_docente';
    protected $primaryKey = 'docente_id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}

?>
