<?php
require_once __DIR__ . "/../core/Model.php";

class Observacion extends Model {
    protected $table = 'tbl_observacion';
    protected $primaryKey = 'observacion_id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        parent::__construct($this->table);
    }
}
?>