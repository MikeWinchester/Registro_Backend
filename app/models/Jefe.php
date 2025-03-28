<?php
require_once __DIR__ . "/../core/Model.php";

class Jefe extends Model {
    protected $table = 'tbl_jefe';
    protected $primaryKey = 'jefe_id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}

?>
