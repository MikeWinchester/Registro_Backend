<?php
require_once __DIR__ . "/../core/Model.php";

class Notas extends Model {
    protected $table = 'tbl_notas';
    protected $primaryKey = 'notas_id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}
?>
