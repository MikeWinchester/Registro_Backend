<?php
require_once __DIR__ . "/../core/Model.php";

class Seccion extends Model {
    protected $table = 'tbl_seccion';
    protected $primaryKey = 'seccion_id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}
?>
