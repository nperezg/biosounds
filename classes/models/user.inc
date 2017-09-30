<?php

namespace classes\models;

use \classes\database\Database;
use \classes\models\Role;

class User {
	
	const TABLE_NAME = "Users";
	const ID = "UserID";
	const NAME = "UserName";
	const FULL_NAME = "UserFullname";
	const EMAIL = "UserEmail";
	const ROLE = "Role";
	const ACTIVE = "UserActive";
	const PASSWORD = "Password";
	const TAG_COLOR = "TagColor";
	

	public function __construct(){
	}
	
	public function getPassword($username){
		if(empty($username))
			return null;
			
		Database::prepareQuery("SELECT Password FROM Users WHERE UserName = :username");	
		$fields = [":username" => $username];
		$result = Database::executeSelect($fields);	
		if(empty($result))
			return NULL;
		
		return $result[0]["Password"];	
	}
	
	public function getPasswordByUserID($userID){
		if(empty($userID))
			return null;
			
		Database::prepareQuery("SELECT Password FROM Users WHERE " . self::ID. " =:userID");	
		$fields = [":userID" => $userID];
		$result = Database::executeSelect($fields);	
		if(empty($result))
			return NULL;
		
		return $result[0][self::PASSWORD];	
	}

	public function getUserID($username){
		Database::prepareQuery("SELECT UserID FROM Users WHERE UserName = :username");
		$fields = [":username" => $username];
		$result = Database::executeSelect($fields);
		if(empty($result))
			return NULL;
			
		return $result[0]["UserID"];
	}
	
	public function getUserName($userID){
		Database::prepareQuery("SELECT " . self::NAME . " FROM Users WHERE " . self::ID . " = :userID");
		$fields = [":userID" => $userID];
		$result = Database::executeSelect($fields);
		if(empty($result))
			return NULL;
			
		return $result[0][self::NAME];
	}
	
	public function getTagColor($userID){
		Database::prepareQuery("SELECT ". self::TAG_COLOR . " FROM Users WHERE " . self::ID. " =:userID");	
		$fields = [":userID" => $userID];
		$result = Database::executeSelect($fields);	
		if(empty($result))
			return NULL;
		
		return $result[0][self::TAG_COLOR];	
	}
	
	public function isUserActive($userID){
		Database::prepareQuery("SELECT UserActive from Users WHERE UserID = :userid");	
		$fields = [":userid" => $userID];		    
		$result = Database::executeSelect($fields);	
		if(empty($result))
			return false;
		
		return $result[0]["UserActive"] == 1 ? true : false;		
	}	
	
	public function isUserAdmin($userID){
		Database::prepareQuery("SELECT STRCMP(Roles.Name, :roleName) AS Result FROM Users LEFT JOIN Roles ON Users.Role = Roles.ID WHERE UserID = :userid");	
		$fields = [":userid" => $userID, ":roleName" => Role::ADMIN_ROLE];		    
		$result = Database::executeSelect($fields);	
		if(empty($result))
			throw new \Exception("User $userID doesn't exist.");
		
		return ($result[0]["Result"] == 0 ? true : false);	
	}
	
	public function getActiveUsers(){
		Database::prepareQuery("SELECT * from Users WHERE UserActive='1' ORDER BY UserActive, UserName");	
		$result = Database::executeSelect();	
		return $result;		
	}
	
    public function getAllUsers(){
		Database::prepareQuery("SELECT * from Users ORDER BY UserActive DESC, UserName");	
		$result = Database::executeSelect();	
		return $result;		
	}
	
	public function countOtherAdminUsers($userID){
		Database::prepareQuery("SELECT COUNT(*) AS AdminCount FROM Users WHERE Role = :adminRoleID AND UserID <> :userid");	
		$result = Database::executeSelect([":userid" => $userID, ":adminRoleID" => Role::ADMIN_ID]);	
		if(empty($result))
			return 0;
		
		return $result[0]["AdminCount"];
	}
	
	public function insertUser($userData){
		if(empty($userData))
			return false;
			
		$fields = "( ";
		$valuesNames = "( ";
		$values = array();
		
		foreach($userData as $key => $value){
			$fields .= $key;
			$valuesNames .= ":".$key;
			$values[":".$key] = $value;
			if(end($userData) !== $value){
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
	
	public function updateUser($userData){
		if(empty($userData))
			return false;
			
		$userID = $userData["itemID"];	
		unset($userData["itemID"]);
		$fields = "";
		$values = array();
		
		foreach($userData as $key => $value){
			$fields .= $key . " = :".$key;
			$values[":".$key] = $value;
			if(end($userData) !== $value)
				$fields .= ", ";
		}
		$values[":userID"] = $userID;
		Database::prepareQuery("UPDATE Users SET " . $fields . " WHERE " . self::ID. "= :userID");
		$result = Database::executeUpdate($values);	
		return $result;
	}
}

?>
