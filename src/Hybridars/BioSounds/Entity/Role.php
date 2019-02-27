<?php

namespace Hybridars\BioSounds\Entity;

use Hybridars\BioSounds\Database\Database;

class Role
{

	const ADMIN_ROLE = 'Administrator';
	const ADMIN_ID = 1;
	
    /**
     * @return array|int
     * @throws \Exception
     */
	public function getRoles()
    {
		Database::prepareQuery('SELECT * FROM role ORDER BY role_id');
		$result = Database::executeSelect();
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
