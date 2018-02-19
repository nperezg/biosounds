<?php

namespace Hybridars\BioSounds\Entity;

use Hybridars\BioSounds\Database\Database;

class File {
	
	public function insertFilePath($userID, $uploadDir)
    {
		Database::prepareQuery("INSERT INTO FilesToAdd (UserID, StartTime, FilesPath) VALUES (:userID, NOW(), :uploadDir)");
		$fields = [":userID" => $userID, ":uploadDir" => $uploadDir];
		$result = Database::executeInsert($fields);
		return $result;
	}
	
	public function insertFileToAdd(array $values)
    {
		Database::prepareQuery("INSERT INTO FilesToAddMembers (FilesToAddID, FullPath, OriginalFilename, Date, Time, SiteID, ColID, DirID, SensorID)
				VALUES (:fileID, :filePath, :fileName, :date, :time, :siteID, :colID, :dirID, :sensorID)");
		$result = Database::executeInsert($values);
		return $result;
	}
}
