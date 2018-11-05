<?php

namespace Hybridars\BioSounds\Entity;

use Hybridars\BioSounds\Database\Database;

abstract class AbstractEntity
{
	
	const TABLE_NAME = ""; 
	const PRIMARY_KEY = "";
	const NAME = "";

    
    public function getBasicList(){
		Database::prepareQuery("SELECT ".static::PRIMARY_KEY.", ".static::NAME." FROM " . static::TABLE_NAME . " ORDER BY ".static::NAME);
		$result = Database::executeSelect();
		return $result;
	}
}
