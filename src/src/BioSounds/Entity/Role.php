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
}
