<?php

namespace classes\models;

use \classes\models\Model;

class Collection extends Model {
	
	const TABLE_NAME = "Collections";
	const PRIMARY_KEY = "ColID";
	const NAME = "CollectionName";
	
	public function __construct() {
    }
}

?>
