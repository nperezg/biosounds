<?php

namespace Hybridars\Entity;

use Hybridars\Database\Database;

class SoundTag
{
	const TABLE_NAME = "SoundsMarks";
	const TAG_ID = "marks_ID";
	const SOUND_ID = "SoundID";
	const TIME_MIN = "time_min";
	const TIME_MAX = "time_max";
	const FREQ_MIN = "freq_min";
	const FREQ_MAX = "freq_max";
	const ANIMAL_ID = "AnimalID";
	const UNCERTAIN = "uncertain";
	const REFERENCE_CALL = "reference_call";
	const USER_ID = "UserID";
	const CALL_DISTANCE = "call_distance_m";
	const DISTANCE_NOT_ESTIMABLE = "distance_not_estimable";
	const NUMBER_INDIVIDUALS = "number_of_individuals";
	const COMMENTS = "comments";
	const DATETIME = "datetime";
	
	public function __construct() {
    }
    
    public function getSoundTag(int $tagID) : array
    {
		$query = "SELECT *, SoundsMarks.AnimalID, Binomial as AnimalName FROM SoundsMarks LEFT JOIN Animals ON SoundsMarks.AnimalID = Animals.AnimalID LEFT JOIN Users ON ".
		self::TABLE_NAME . "." . self::USER_ID. "=" . User::TABLE_NAME . "." . User::ID. " WHERE marks_ID = :tagID";

		$values[":tagID"] = $tagID;
		
		Database::prepareQuery($query);
		$result = Database::executeSelect($values);	
		if(empty($result))
			 throw new \Exception("Sound Tag $tagID doesn't exist.");
					
		return $result[0];
	}	
    
    public function getSoundTags($soundID, $userID = NULL){
		$query = "SELECT marks_ID, SoundID, time_min, time_max, freq_min, freq_max, mark_tag, UserID, Binomial as AnimalName, 
		(SELECT COUNT(*) FROM Reviews WHERE sound_tag = marks_ID) as reviews, ((time_max-time_min)+(freq_max-freq_min)) AS time, " . 
		self::CALL_DISTANCE . ", " . self::DISTANCE_NOT_ESTIMABLE .
		" FROM SoundsMarks LEFT JOIN Animals ON SoundsMarks.AnimalID = Animals.AnimalID WHERE SoundID = :soundID";

		$values[":soundID"] = $soundID;
		
		if(!empty($userID)) {
			$query .= " AND UserID = :userID";
			$values[":userID"] = $userID;
		}
		$query .= " ORDER BY time";	
		
		Database::prepareQuery($query);
		$result = Database::executeSelect($values);	
		return $result;
	}
	
    public function insertSoundTag($tagData)
    {
		if(empty($tagData))
			return false;
			
		$fields = "( ";
		$valuesNames = "( ";
		$values = array();
		$i = 1;
		foreach($tagData as $key => $value){
			$fields .= $key;
			$valuesNames .= ":".$key;
			$values[":".$key] = $value;
			if($i < count($tagData)){
				$fields .= ", ";
				$valuesNames .= ", ";
			} else {
				$fields .= " )";
				$valuesNames .= " )";
			}
			$i++;			
		}
		$query = "INSERT INTO SoundsMarks $fields VALUES $valuesNames";
		Database::prepareQuery($query);
		$result = Database::executeInsert($values);	
		return $result;
	}
	
	public function updateSoundTag($tagData){
		if(empty($tagData) || empty($tagData[SoundTag::TAG_ID]))
			return false;
			
		$fieldValues = "";	
		$values = array();
		$i = 1;
		foreach($tagData as $key => $value){
			if($key != SOUNDTAG::TAG_ID){
				$fieldValues .= $key . "=" . ":".$key;
				if($i < count($tagData))
					$fieldValues .= ", ";
			}			
			$values[":".$key] = $value;
			$i++;			
		}		
		$query = "UPDATE SoundsMarks SET $fieldValues WHERE " . SOUNDTAG::TAG_ID . " = :" . SOUNDTAG::TAG_ID;
		Database::prepareQuery($query);
		$result = Database::executeUpdate($values);	
		return $result;
	}
	
	public function deleteSoundTag($tagID){
		Database::prepareQuery("DELETE FROM " . self::TABLE_NAME . " WHERE " . self::TAG_ID . "=:tagID");
		$values = [":tagID" => $tagID];	
		$result = Database::executeDelete($values);	
		return $result;
	}
}
