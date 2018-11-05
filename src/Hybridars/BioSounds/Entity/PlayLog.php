<?php

namespace Hybridars\BioSounds\Entity;

use Hybridars\BioSounds\Database\Database;

class PlayLog
{
	const TABLE_NAME = "play_log";
	const ID = "play_log_id";
	const RECORDING_ID = "recording_id";
	const USER_ID = "user_id";
	const START_TIME = "start_time";
	const STOP_TIME = "stop_time";

    /**
     * @param array $data
     * @return array|bool|int
     * @throws \Exception
     */
    public function insert(array $data)
    {
		if (empty($data)) {
		    return false;
        }

		$fields = '( ';
		$valuesNames = '( ';
		$values = [];
		$i = 1;

		foreach($data as $key => $value) {
			$fields .= $key;
			$valuesNames .= ":".$key;
			$values[":".$key] = $value;
			if ($i < count($data)) {
				$fields .= ", ";
				$valuesNames .= ", ";
			} else {
				$fields .= " )";
				$valuesNames .= " )";
			}
			$i++;			
		}

		$query = 'INSERT INTO ' . self::TABLE_NAME. " $fields VALUES $valuesNames";
		Database::prepareQuery($query);
		return Database::executeInsert($values);
	}
}
