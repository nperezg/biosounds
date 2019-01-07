<?php

namespace Hybridars\BioSounds\Entity;

use Hybridars\BioSounds\Database\Database;

class Tag
{
	const TABLE_NAME = "tag";
	const ID = "tag_id";
    const SPECIES_ID = "species_id";
	const RECORDING_ID = "recording_id";
    const USER_ID = "user_id";
	const MIN_TIME = "min_time";
	const MAX_TIME = "max_time";
	const MIN_FREQ = "min_freq";
	const MAX_FREQ = "max_freq";
	const UNCERTAIN = "uncertain";
	const REFERENCE_CALL = "reference_call";
	const CALL_DISTANCE = "call_distance_m";
	const DISTANCE_NOT_ESTIMABLE = "distance_not_estimable";
	const NUMBER_INDIVIDUALS = "number_of_individuals";
	const COMMENTS = "comments";
	const TYPE = 'type';

    /**
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public function get(int $id) : array
    {
		$query = 'SELECT ' . self::ID . ', ' . self::RECORDING_ID . ', ' . User::FULL_NAME . ', ';
        $query .= self::TABLE_NAME . '.' . self::USER_ID . ' as user_id, ' . self::MIN_TIME .  ', ' . self::MAX_TIME .  ', ';
        $query .= self::MIN_FREQ .  ', ' . self::MAX_FREQ . ', ';
        $query .= self::UNCERTAIN . ', ' . self:: REFERENCE_CALL . ', '. self::CALL_DISTANCE . ', ';
        $query .= self::DISTANCE_NOT_ESTIMABLE . ', '. self:: NUMBER_INDIVIDUALS . ', ' . self::COMMENTS . ', ';
        $query .= self::TYPE . ', ' . self::TABLE_NAME . '.' . self::SPECIES_ID . ', ' . Species::BINOMIAL . ' ';
        $query .= 'FROM ' . self::TABLE_NAME . ' ';
		$query .= 'LEFT JOIN ' . Species::TABLE_NAME . ' ON ';
		$query .= self::TABLE_NAME . '.' . self::SPECIES_ID . ' = ' . Species::TABLE_NAME .'.'. Species::ID . ' ';
		$query .= 'LEFT JOIN Users ON '. self::TABLE_NAME . '.' . self::USER_ID. ' = ';
		$query .= User::TABLE_NAME . '.' . User::ID. ' ';
		$query .= 'WHERE ' . self::TABLE_NAME . '.' . self::ID . ' = :id';

		Database::prepareQuery($query);

        $values[':id'] = $id;
		$result = Database::executeSelect($values);

		if(empty($result)) {
            throw new \Exception("Tag $id doesn't exist.");
        }
					
		return $result[0];
	}

    /**
     * @param $recordingId
     * @param null $userId
     * @return array|int
     * @throws \Exception
     */
    public function getList($recordingId, $userId = null)
    {
		$query = 'SELECT ' . self::ID . ', ' . self::RECORDING_ID . ', ';
		$query .= self::MIN_TIME. ', ' . self::MAX_TIME . ', ' . self::MIN_FREQ . ', ' . self::MAX_FREQ . ', ';
		$query .= self::USER_ID . ', ' . Species::BINOMIAL . ', (SELECT COUNT(*) FROM Reviews ';
		$query .= 'WHERE sound_tag = ' . self::TABLE_NAME . '.' .self::ID . ') as reviews, ';
		$query .= '((' . self::MAX_TIME . '-'. self::MIN_TIME . ')+(' . self::MAX_FREQ . '-'. self::MIN_FREQ ;
		$query .= ')) AS time, ' . self::CALL_DISTANCE . ', '. self::DISTANCE_NOT_ESTIMABLE . ' ';
		$query .= 'FROM ' . self::TABLE_NAME . ' LEFT JOIN ' . Species::TABLE_NAME . ' ON ';
		$query .= self::TABLE_NAME . '.' . self::SPECIES_ID . ' = ' . Species::TABLE_NAME . '.' . Species::ID;
		$query .= ' WHERE ' . self::RECORDING_ID . ' = :recordingId';

		$values[':recordingId'] = $recordingId;
		
		if (!empty($userId)) {
			$query .= ' AND ' . self::USER_ID . ' = :userId';
			$values[':userId'] = $userId;
		}
		$query .= ' ORDER BY time';
		
		Database::prepareQuery($query);
		$result = Database::executeSelect($values);

		return $result;
	}

    /**
     * @param $data
     * @return int
     * @throws \Exception
     */
    public function insert($data) : int
    {
		if (empty($data)) {
		    return false;
        }

		$fields = '( ';
		$valuesNames = '( ';
		$values = array();
		$i = 1;
		foreach ($data as $key => $value)
		{
			$fields .= $key;
			$valuesNames .= ':'.$key;
			$values[':'.$key] = $value;
			if($i < count($data)){
				$fields .= ', ';
				$valuesNames .= ', ';
			} else {
				$fields .= ' )';
				$valuesNames .= ' )';
			}
			$i++;			
		}
		$query = 'INSERT INTO ' . self::TABLE_NAME . " $fields VALUES $valuesNames";
		Database::prepareQuery($query);
		$result = Database::executeInsert($values);	
		return $result;
	}

    /**
     * @param $data
     * @return array|bool|int
     * @throws \Exception
     */
	public function update($data)
    {
		if (empty($data) || empty($data[Tag::ID])) {
		    return false;
        }

		$fieldValues = '';
		$values = array();
		$i = 1;
		foreach ($data as $key => $value)
		{
			if($key != Tag::ID){
				$fieldValues .= $key . '=' . ':'.$key;
				if ($i < count($data)) {
                    $fieldValues .= ', ';
                }
			}			
			$values[':'.$key] = $value;
			$i++;			
		}		
		$query = 'UPDATE ' . self::TABLE_NAME . " SET $fieldValues ";
		$query.= 'WHERE ' . self::ID . ' = :' . self::ID;

		Database::prepareQuery($query);
		$result = Database::executeUpdate($values);

		return $result;
	}

    /**
     * @param $id
     * @return array|int
     * @throws \Exception
     */
	public function delete($id)
    {
		Database::prepareQuery('DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::ID . '=:id');

		$values = [':id' => $id];
		$result = Database::executeDelete($values);

		return $result;
	}
}
