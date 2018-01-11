<?php

namespace Hybridars\Entity;

use Hybridars\Database\Database;

abstract class AbstractEntity {
	
	const TABLE_NAME = ""; 
	const PRIMARY_KEY = "";
	const NAME = "";
	
    public function getFullList(){
		return $this->getFullListOrderBy(static::NAME);
	}
	
    public function getFullListOrderBy($order){
		Database::prepareQuery("SELECT * FROM " . static::TABLE_NAME . " ORDER BY $order");
		$result = Database::executeSelect();
		return $result;
	}
    
    public function getBasicList(){
		Database::prepareQuery("SELECT ".static::PRIMARY_KEY.", ".static::NAME." FROM " . static::TABLE_NAME . " ORDER BY ".static::NAME);
		$result = Database::executeSelect();
		return $result;
	}
	
	public function getPrimaryKey(array $value){	
		if(!empty($value[static::PRIMARY_KEY]))
			return $value[static::PRIMARY_KEY];
		else
			throw new \Exception("There's no primary key in this array.");
	}
	
	public function getName(array $value){
		if(!empty($value[static::NAME]))
			return $value[static::NAME];
		else
			throw new \Exception("There's no name in this array.");
	}
	
	public function getObject($objectID){
		Database::prepareQuery("SELECT * FROM " . static::TABLE_NAME . " WHERE " . static::PRIMARY_KEY . " = :objectID");
		$fields = [":objectID" => $objectID];
		$result = Database::executeSelect($fields);
		if(empty($result))
			throw new \Exception("Table " . static::TABLE_NAME . " doesn't contain the row with ID: $objectID.");
		
		return $result[0];
	}
}
