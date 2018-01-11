<?php

namespace Hybridars\Models;

use Hybridars\Database\Database;
use Hybridars\Entity\Collection;

class UserPermission
{
	const TABLE_NAME = "UserPermissions";
	const USER = "user";
	const COLLECTION = "collection";
	const PERMISSION = "permission";

	public function getUserColPermission($userID, $colID){
		Database::prepareQuery("SELECT " . self::PERMISSION . " FROM " . self::TABLE_NAME . " WHERE " . self::USER. "=:userID AND " . self::COLLECTION. "=:colID" );	
		$fields = [":userID" => $userID, ":colID" => $colID];	
		$result = Database::executeSelect($fields);	
		if(empty($result))
			return null;
		return $result[0][self::PERMISSION];		
	}
	
	public function getColPermissionsByUser($userID){
		Database::prepareQuery("SELECT * FROM " . Collection::TABLE_NAME . " LEFT JOIN " . self::TABLE_NAME . " ON " . 
		self::COLLECTION. " = " . Collection::PRIMARY_KEY . " AND " . self::USER. " =:userID ORDER BY " . Collection::PRIMARY_KEY);	
		$fields = [":userID" => $userID];	
		$result = Database::executeSelect($fields);	
		return $result;		
	}
	
	public function hasUserColPermissions($userID, $colID){
		Database::prepareQuery("SELECT * FROM " . Collection::TABLE_NAME . " WHERE " . self::USER. " =:userID AND " . self::COLLECTION . " =:colID");	
		$fields = [":userID" => $userID, ":colID" => $colID];	
		$result = Database::executeSelect($fields);	
		if(empty($result))
			return false;
		return true;		
	}
	
	public function insertUserPermission($permData){
		if(empty($permData))
			return false;
			
		$fields = "( ";
		$valuesNames = "( ";
		$values = array();
		
		foreach($permData as $key => $value){
			$fields .= $key;
			$valuesNames .= ":".$key;
			$values[":".$key] = $value;
			if(end($permData) !== $value){
				$fields .= ", ";
				$valuesNames .= ", ";
			}
		}
		$fields .= " )";
		$valuesNames .= " )";
		Database::prepareQuery("INSERT INTO " . self::TABLE_NAME . " $fields VALUES $valuesNames");
		$result = Database::executeInsert($values);	
		return $result;
	}
	
	public function updateUserPermission($permData){
		if(empty($permData))
			return false;
			
		$userID = $permData[self::USER];	
		unset($permData[self::USER]);
		$colID = $permData[self::COLLECTION];	
		unset($permData[self::COLLECTION]);
		
		$fields = "";
		$values = array();
		
		foreach($permData as $key => $value){
			$fields .= $key . " = :".$key;
			$values[":".$key] = $value;
			if(end($permData) !== $value)
				$fields .= ", ";
		}
		$values[":userID"] = $userID;
		$values[":colID"] = $colID;
		Database::prepareQuery("UPDATE " . self::TABLE_NAME . " SET $fields WHERE " . self::USER. "=:userID AND " . self::COLLECTION . "= :colID");
		$result = Database::executeUpdate($values);	
		return $result;
	}
	
	public function deleteUserPermission($userID, $colID){
		Database::prepareQuery("DELETE FROM " . self::TABLE_NAME . " WHERE " . self::USER . "=:userID AND " . self::COLLECTION . "=:colID");
		$values = [":userID" => $userID, ":colID" => $colID];	
		$result = Database::executeDelete($values);	
		return $result;
	}
}
