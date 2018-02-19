<?php

namespace Hybridars\BioSounds\Entity;

use Hybridars\BioSounds\Database\Database;

class SoundListenLog
{
	const TABLE_NAME = "SoundListenLogs";
	const ID = "ListenID";
	const SOUND_ID = "SoundID";
	const USER_ID = "UserID";
	const START_TIME = "StartTime";
	const STOP_TIME = "StopTime";

    public function insertSoundLog($logData){
		if(empty($logData))
			return false;
			
		$fields = "( ";
		$valuesNames = "( ";
		$values = array();
		$i = 1;
		foreach($logData as $key => $value){
			$fields .= $key;
			$valuesNames .= ":".$key;
			$values[":".$key] = $value;
			if($i < count($logData)){
				$fields .= ", ";
				$valuesNames .= ", ";
			} else {
				$fields .= " )";
				$valuesNames .= " )";
			}
			$i++;			
		}

		$query = "INSERT INTO " . self::TABLE_NAME. " $fields VALUES $valuesNames";
		Database::prepareQuery($query);
		$result = Database::executeInsert($values);	
		return $result;
	}
}
