<?php
require_once __DIR__ . "/../core/Model.php";

class Edificio extends Model {
    protected $table = 'tbl_edificio';
    protected $primaryKey = 'edificio_id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}

?>