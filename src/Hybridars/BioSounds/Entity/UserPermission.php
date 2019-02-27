<?php

namespace Hybridars\BioSounds\Entity;

use Hybridars\BioSounds\Database\Database;

class UserPermission
{
	const TABLE_NAME = "user_permission";
	const USER = "user_id";
	const COLLECTION = "collection_id";
	const PERMISSION = "permission_id";

    /**
     * @param int $userId
     * @param int $colId
     * @return int|null
     * @throws \Exception
     */
	public function getUserColPermission(int $userId, int $colId): ?int
    {
		Database::prepareQuery(
		    'SELECT permission_id FROM user_permission WHERE user_id = :userId AND collection_id = :colId'
        );

		if (empty($result = Database::executeSelect([":userId" => $userId, ":colId" => $colId]))) {
            return null;
        }
		return $result[0][self::PERMISSION];		
	}

    /**
     * @param int $userId
     * @return array
     * @throws \Exception
     */
	public function getColPermissionsByUser(int $userId): array
    {
		Database::prepareQuery(
		    'SELECT collection.collection_id, collection.name, collection.author, user_permission.permission_id ' .
            'FROM collection LEFT JOIN user_permission ON user_permission.collection_id = ' .
            Collection::TABLE_NAME. '.' . Collection::PRIMARY_KEY . ' AND user_id = :userId ORDER BY ' .
            Collection::TABLE_NAME. '.' . Collection::PRIMARY_KEY
        );
		return Database::executeSelect([':userId' => $userId]);
	}

    /**
     * @param int $userId
     * @param int $colId
     * @return bool
     * @throws \Exception
     */
	public function hasUserColPermissions(int $userId, int $colId): bool
    {
		Database::prepareQuery(
		    'SELECT * FROM ' . Collection::TABLE_NAME . ' WHERE user_id = :userId AND collection_id = :colId'
        );

		if (empty(Database::executeSelect([':userId' => $userId, ':colId' => $colId]))) {
            return false;
        }
		return true;		
	}

    /**
     * @param array $permissionData
     * @return int|null
     * @throws \Exception
     */
	public function insertUserPermission(array $permissionData): ?int
    {
		if (empty($permissionData)) {
		    return false;
        }

		$fields = '( ';
		$valuesNames = '( ';
		$values = [];
		
		foreach ($permissionData as $key => $value) {
			$fields .= $key;
			$valuesNames .= ':' . $key;
			$values[':'.$key] = $value;
			if (end($permissionData) !== $value) {
				$fields .= ', ';
				$valuesNames .= ', ';
			}
		}
		$fields .= ' )';
		$valuesNames .= ' )';
		Database::prepareQuery("INSERT INTO user_permission $fields VALUES $valuesNames");
		return Database::executeInsert($values);
	}

    /**
     * @param array $permissionData
     * @return int|null
     * @throws \Exception
     */
	public function updateUserPermission(array $permissionData): ?int
    {
		if (empty($permissionData)) {
            return false;
        }
			
		$userId = $permissionData[self::USER];
		unset($permissionData[self::USER]);
		$colId = $permissionData[self::COLLECTION];
		unset($permissionData[self::COLLECTION]);
		
		$fields = '';
		$values = [];
		
		foreach ($permissionData as $key => $value) {
			$fields .= $key . " = :".$key;
			$values[":".$key] = $value;
			if (end($permissionData) !== $value) {
                $fields .= ", ";
            }
		}
		$values[':userId'] = $userId;
		$values[':colId'] = $colId;
		Database::prepareQuery("UPDATE user_permission SET $fields WHERE user_id = :userId AND collection_id = :colId");
		return Database::executeUpdate($values);
	}

    /**
     * @param int $userId
     * @param int $colId
     * @return int|null
     * @throws \Exception
     */
	public function deleteUserPermission(int $userId, int $colId): ?int
    {
		Database::prepareQuery('DELETE FROM user_permission WHERE user_id = :userId AND collection_id =:colId');
		return Database::executeDelete([':userId' => $userId, ':colId' => $colId]);
	}
}
