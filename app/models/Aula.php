<?php
require_once __DIR__ . "/../core/Model.php";

class Aula extends Model {
    protected $table = 'tbl_aula';
    protected $primaryKey = 'aula_id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}

?>