<?php 

namespace classes\models;

use \classes\database\Database;

class Settings {
	
	const TABLE_NAME = "Settings";
	const NAME = "Name";
	const VALUE = "Value";
	
	const PROJECT_NAME = "projectName";
	const PROJECT_DESCRIPTION = "projectDescription";
	const FFT = "FFT";
	const FILES_LICENSE = "filesLicense";	
	const FILES_LICENSE_DETAIL = "filesLicenseDetail";	

	public function __construct(){
	}
	
	public function getSettings(){
		Database::prepareQuery("SELECT * FROM Settings");
		$result = Database::executeSelect();
		$values = array();
		foreach($result as $data){
			$values[$data["Name"]] = $data["Value"];
		}
		return $values;
	}
	
	public function getSetting($name){
		Database::prepareQuery("SELECT Value FROM Settings WHERE Name = :name");
		$result = Database::executeSelect([":name" => $name]);
		if(empty($result))
			return NULL;
		
		return $result[0]["Value"];			
	}
	
	public function updateSetting($name, $value){
		Database::prepareQuery("UPDATE " . self::TABLE_NAME. " SET " . self::VALUE . "=:value WHERE " . self::NAME. "=:name");
		$values = [":name" => $name, ":value" => $value];
		return Database::executeUpdate($values);
	}
}

?>
