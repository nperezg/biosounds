<?php

namespace Hybridars\BioSounds\Entity;

use Hybridars\BioSounds\Database\Database;

class TagReview
{
	const TABLE_NAME = "Reviews";
	const SOUND_TAG = "sound_tag";
	const USER = "user";
	const STATUS = "status";
	const ANIMAL = "animal";
	const COMMENTS = "comments";
	const DATE = "date";

	//TODO: Create ReviewStatus model
	const STATUS_TABLE_NAME = "ReviewStatus";
	const STATUS_NAME = "description";

    public function getTagReviews($tagID){
		$query = "SELECT " . User::FULL_NAME. ", " . Species::BINOMIAL . ", " . self::STATUS_NAME . ", " . self::DATE . " FROM " . self::TABLE_NAME .
		         " LEFT JOIN " . Species::TABLE_NAME . " ON "  . Species::TABLE_NAME .  "." . Species::ID . " = " . self::ANIMAL .
		         " LEFT JOIN " . User::TABLE_NAME . " ON " . User::TABLE_NAME . "." . User::ID . " = " . self::USER . 
		         " LEFT JOIN " . self::STATUS_TABLE_NAME . " ON " . self::STATUS_TABLE_NAME . ".id = " . self::STATUS . " WHERE " . self::SOUND_TAG. "=:tagID ORDER BY " . self::DATE;

		$values[":tagID"] = $tagID;
		Database::prepareQuery($query);
		$result = Database::executeSelect($values);	
		return $result;
	}
	
	public function hasUserReviewed($userID, $tagID){
		$query = "SELECT COUNT(*) as NumReviews FROM " . self::TABLE_NAME. " WHERE " . self::USER . "=:userID AND " . self::SOUND_TAG. "=:tagID";
		$values = [":userID" => $userID, ":tagID" => $tagID];
		Database::prepareQuery($query);
		$result = Database::executeSelect($values);
		if(!empty($result) && $result[0]["NumReviews"] > 0)	
			return true;
		return false;	
	}
	
    public function insertTagReview($tagData){
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
		$query = "INSERT INTO " . self::TABLE_NAME. " $fields VALUES $valuesNames";
		Database::prepareQuery($query);
		$result = Database::executeInsert($values);	
		return $result;
	}
}
