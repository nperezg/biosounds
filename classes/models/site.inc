<?php

namespace classes\models;

use \classes\database\Database;

class Site extends Model{
	
	const TABLE_NAME = "Sites";
	const PRIMARY_KEY = "SiteID";
	const NAME = "SiteName";
	
	public function __construct() {
    }
}

?>
