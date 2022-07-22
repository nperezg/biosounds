<?php

namespace BioSounds\Entity;

use BioSounds\Provider\BaseProvider;

class UserPermission extends BaseProvider
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
        $this->database->prepareQuery(
            'SELECT permission_id FROM user_permission WHERE user_id = :userId AND collection_id = :colId'
        );

        if (empty($result = $this->database->executeSelect([":userId" => $userId, ":colId" => $colId]))) {
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
        $this->database->prepareQuery(
            'SELECT collection.collection_id, collection.name, user_permission.permission_id ' .
            'FROM collection LEFT JOIN user_permission ON user_permission.collection_id = ' .
            Collection::TABLE_NAME . '.' . Collection::PRIMARY_KEY . ' AND user_permission.user_id = :userId ORDER BY ' .
            Collection::TABLE_NAME . '.' . Collection::PRIMARY_KEY
        );
        return $this->database->executeSelect([':userId' => $userId]);
    }

    /**
     * @param array $permissionData
     * @return int|null
     * @throws \Exception
     */
    public function insert(array $permissionData): ?int
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
            $values[':' . $key] = $value;
            if (end($permissionData) !== $value) {
                $fields .= ', ';
                $valuesNames .= ', ';
            }
        }
        $fields .= ' )';
        $valuesNames .= ' )';
        $this->database->prepareQuery("INSERT INTO user_permission $fields VALUES $valuesNames");
        return $this->database->executeInsert($values);
    }

    /**
     * @param int $userId
     * @param int $colId
     * @return int|null
     * @throws \Exception
     */
    public function delete(int $userId, int $colId): ?int
    {
        $this->database->prepareQuery('DELETE FROM user_permission WHERE user_id = :userId AND collection_id =:colId');
        return $this->database->executeDelete([':userId' => $userId, ':colId' => $colId]);
    }
}
