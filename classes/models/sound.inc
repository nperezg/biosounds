<?php

namespace classes\models;

use \classes\database\Database;

class Sound {
	
	const TABLE_NAME = "Sounds";
	const ID = "SoundID"; 
	const COL_ID = "ColID"; 
	const DIR_ID = "DirID"; 
	const FORMAT = "SoundFormat";
	const FILE_SIZE = "FileSize";
	const NAME = "SoundName";
	const ORIGINAL_FILENAME = "OriginalFilename";
	const DATE = "Date";
	const TIME = "Time";
	const DURATION = "Duration";
	const NUM_CHANNELS = "Channels";
	const SAMPLING_RATE = "SamplingRate";
	const SOUND_STATUS = 'SoundStatus';
	const AUDIO_PREVIEW = 'AudioPreviewFilename'; 
	
	public function __construct() {
    }
    
    public function countSoundsCollection($colID){
		Database::prepareQuery("SELECT COUNT(*) AS NumSounds FROM Sounds LEFT JOIN SoundsImages ON Sounds.SoundID = SoundsImages.SoundID WHERE ColID = :colID AND SoundStatus != '9'
		 AND ImageType='spectrogram-small'");
		/* ??
		 * #If user is not logged in, add check for QF
			if ($pumilio_loggedin == FALSE) {
				$qf_check = "AND Sounds.QualityFlagID>='$default_qf'";
				}
			else {
				$qf_check = "";
				}
		 * */
		$fields = [":colID" => $colID];
		$result = Database::executeSelect($fields);
		if(empty($result))
			return 0;
					
		return $result[0]["NumSounds"];
	}
	
	public function getSoundsPagByCollection($colID, $sqlLimit, $sqlOffset){
		//$query = "SELECT *, DATE_FORMAT(Date, '%d-%b-%Y') AS Date_h FROM Sounds WHERE ColID='$ColID' 
				//AND Sounds.SoundStatus!='9' $qf_check ORDER BY $order_byq $order_dir LIMIT $sql_limit";
				
		//Database::prepareQuery("SELECT *, DATE_FORMAT(Date, '%d-%b-%Y') AS Date FROM Sounds  
		//WHERE ColID = :colID AND Sounds.SoundStatus != '9' ORDER BY SoundName LIMIT :sqlLimit OFFSET :sqlOffset");				
				
		Database::prepareQuery("SELECT *, DATE_FORMAT(Date, '%d-%b-%Y') AS Date, ImageFile FROM Sounds LEFT JOIN SoundsImages ON Sounds.SoundID = SoundsImages.SoundID 
		WHERE ColID = :colID AND Sounds.SoundStatus != '9' AND ImageType='spectrogram-small' ORDER BY SoundName LIMIT :sqlLimit OFFSET :sqlOffset");		
		$fields = [":colID" => $colID, ":sqlLimit" => $sqlLimit, ":sqlOffset" => $sqlOffset];
		$result = Database::executeSelect($fields);

		if(empty($result))
			return NULL;
					
		return $result;
	}
	
	public function getSoundsByCollection($colID, $sqlLimit, $sqlOffset){
		Database::prepareQuery("SELECT " . self::ID . ", " . self::ORIGINAL_FILENAME . ", " . self::NAME. 
		", DATE_FORMAT(" . self::DATE . ", '%Y-%m-%d') AS " . self::DATE .
		", DATE_FORMAT(" . self::TIME . ", '%H:%i:%s') AS " . self::TIME . " FROM Sounds WHERE ColID = :colID AND Sounds.SoundStatus != '9' 
		 ORDER BY " . self::ID. " LIMIT :sqlLimit OFFSET :sqlOffset");		
		$fields = [":colID" => $colID, ":sqlLimit" => $sqlLimit, ":sqlOffset" => $sqlOffset];
		$result = Database::executeSelect($fields);

		if(empty($result))
			return NULL;
					
		return $result;
	}

	public function getSound($soundID){
		$query = "SELECT *, (SELECT ImageFile FROM SoundsImages WHERE Sounds.SoundID = SoundsImages.SoundID ";
		$query .= "AND ImageType = 'spectrogram-player') AS ImageFile ";
		$query .= "FROM Sounds WHERE Sounds.SoundID = :soundID AND SoundStatus != '9'";
		Database::prepareQuery($query);
		$fields = [":soundID" => $soundID];
		$result = Database::executeSelect($fields);
		if(empty($result))
			 throw new \Exception("Sound $soundID doesn't exist.");
					
		return $result[0];
	}
	
	public function insertSound($soundData){
		if(empty($soundData))
			return false;
			
		$fields = "( ";
		$valuesNames = "( ";
		$values = array();
		
		foreach($soundData as $key => $value){
			$fields .= $key;
			$valuesNames .= ":".$key;
			$values[":".$key] = $value;
			if(end($soundData) !== $value){
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
	
	public function updateSound($soundData){
		if(empty($soundData))
			return false;
			
		$soundID = $soundData["itemID"];	
		unset($soundData["itemID"]);
		$fields = array();
		$values = array();
		
		foreach($soundData as $key => $value){
			$fields[] = $key . " = :".$key;
			$values[":".$key] = $value;
		}
		$values[":soundID"] = $soundID;
		Database::prepareQuery("UPDATE " . self::TABLE_NAME. " SET " . implode(", ", $fields) . " WHERE " . self::ID. "= :soundID");
		$result = Database::executeUpdate($values);	
		return $result;
	}
	
	public function getImages($id)
	{
		$fields = [':soundID' => $id];
		Database::prepareQuery("SELECT ImageFile FROM SoundsImages WHERE " . self::ID . ' = :soundID' );
		return Database::executeSelect($fields);	
	}
	
	public function deleteImages($id)
	{
		$fields = [':soundID' => $id];
		Database::prepareQuery("DELETE FROM SoundsImages WHERE " . self::ID . ' = :soundID' );
		return Database::executeDelete($fields);	
	}
	
	public function deleteTags($id)
	{
		$fields = [':soundID' => $id];
		Database::prepareQuery("DELETE FROM SoundsMarks WHERE " . self::ID . ' = :soundID' );
		return Database::executeDelete($fields);	
	}
	
	public function deleteListenLogs($id)
	{
		$fields = [':soundID' => $id];
		Database::prepareQuery("DELETE FROM SoundListenLogs WHERE " . self::ID . ' = :soundID' );
		return Database::executeDelete($fields);	
	}
}

?>
