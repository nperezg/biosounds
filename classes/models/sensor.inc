<?php

namespace classes\models;

use \classes\database\Database;

class Sensor extends Model {
	
	const TABLE_NAME = "Sensors";
	const PRIMARY_KEY = "SensorID";
	const NAME = "Recorder";
	
	public function __construct() {
    }
}

?>
