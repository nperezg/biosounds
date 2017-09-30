<?php

namespace classes\models;

use \classes\database\Database;

class Role {

	const ADMIN_ROLE = "Administrator";	
	const ADMIN_ID = 1;
	
	public function __construct(){
	}
	
	public function getRoles(){
		Database::prepareQuery("SELECT * FROM Roles ORDER BY ID");
		$result = Database::executeSelect();
		return $result;
	}
	
	public function isRoleAdmin($role){
		return $role == self::ADMIN_ID ? true : false;
	}
	
}

?>
