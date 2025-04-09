<?php
require_once __DIR__ . "/../core/Model.php";

class InfoMatricula extends Model {
    protected $table = 'tbl_info_matricula';
    protected $primaryKey = ['inicio', 'fin'];
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}

?>