<?php

namespace BioSounds\Entity;

use BioSounds\Provider\BaseProvider;

class Role extends BaseProvider
{

    const ADMIN_ROLE = 'Administrator';
    const ADMIN_ID = 1;

    /**
     * @return array|int
     * @throws \Exception
     */
    public function getRoles()
    {
        $this->database->prepareQuery('SELECT * FROM role ORDER BY role_id');
        $result = $this->database->executeSelect();
        return $result;
    }

    /**
     * @param $role
     * @return bool
     */
    public function isRoleAdmin($role)
    {
        return $role == self::ADMIN_ID ? true : false;
    }

    /**
     * @return array|int
     * @throws \Exception
     */
    public function getMyRole(int $userId): array
    {
        $this->database->prepareQuery('SELECT role.* FROM role inner JOIN user on  user.role_id = role.role_id WHERE user.user_id = :userId');
        $result = $this->database->executeSelect([":userId" => $userId]);
        return $result[0];
    }
}
