<?php

namespace Hybridars\Entity;

use Hybridars\Database\Database;

class Permission {
	
	const TABLE_NAME = "Permissions";
	const ID = "id";
	const DESCRIPTION = "description";
	const VIEW = "View";
	const REVIEW = "Review";

	public function isReviewPermission($id){
		Database::prepareQuery("SELECT " . self::DESCRIPTION . " FROM " . self::TABLE_NAME. " WHERE " . self::ID . " = :id");	
		$fields = [":id" => $id];		    
		$result = Database::executeSelect($fields);	
		if(empty($result))
			return false;
		
		return $result[0][self::DESCRIPTION ] == self::REVIEW ? true : false;		
	}	
	
	public function isViewPermission($id){
		Database::prepareQuery("SELECT " . self::DESCRIPTION . " FROM " . self::TABLE_NAME. " WHERE " . self::ID . " = :id");	
		$fields = [":id" => $id];		    
		$result = Database::executeSelect($fields);	
		if(empty($result))
			return false;
		
		return $result[0][self::DESCRIPTION ] == self::VIEW ? true : false;		
	}
	
	public function getViewID(){
		Database::prepareQuery("SELECT " . self::ID . " FROM " . self::TABLE_NAME. " WHERE " . self::DESCRIPTION . " =:name");	
		$fields = [":name" => self::VIEW];	
		$result = Database::executeSelect($fields);	
		if(empty($result))
			return NULL;
		
		return $result[0][self::ID];		
	}	
	
	public function getReviewID(){
		Database::prepareQuery("SELECT " . self::ID . " FROM " . self::TABLE_NAME. " WHERE " . self::DESCRIPTION . " =:name");
		$fields = [":name" => self::REVIEW];	
		$result = Database::executeSelect($fields);	
		if(empty($result))
			return NULL;
		
		return $result[0][self::ID];		
	}	
}
