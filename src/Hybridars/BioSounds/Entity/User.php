<?php

namespace Hybridars\BioSounds\Entity;

use Hybridars\BioSounds\Database\Database;

class User
{
	const TABLE_NAME = "user";
	const ID = "user_id";
	const NAME = "username";
	const FULL_NAME = "name";
	const EMAIL = "email";
	const ROLE = "role";
	const ACTIVE = "active";
	const PASSWORD = "password";
	const TAG_COLOR = "color";

    /**
     * @param string $username
     * @return string|null
     * @throws \Exception
     */
	public function getPassword(string $username): ?string
    {
		if (empty($username)) {
            return null;
        }
			
		Database::prepareQuery('SELECT password FROM user WHERE username = :username');
		if (empty($result = Database::executeSelect([":username" => $username]))) {
		    return null;
        }

		return $result[0]["password"];
	}

    /**
     * @param int $userId
     * @return string|null
     * @throws \Exception
     */
	public function getPasswordByUserId(int $userId): ?string
    {
		if (empty($userId)) {
		    return null;
        }

		Database::prepareQuery('SELECT password FROM user WHERE user_id = :userId');
		if (empty($result = Database::executeSelect([':userId' => $userId]))) {
		    return null;
        }

		return $result[0][self::PASSWORD];	
	}

    /**
     * @param string $username
     * @return int|null
     * @throws \Exception
     */
	public function getUserId(string $username): ?int
    {
		Database::prepareQuery('SELECT user_id FROM user WHERE username = :username');
		if (empty($result = Database::executeSelect([":username" => $username]))) {
		    return null;
        }

		return $result[0]['user_id'];
	}

    /**
     * @param int $userId
     * @return string|null
     * @throws \Exception
     */
	public function getUserName(int $userId): ?string
    {
		Database::prepareQuery('SELECT username FROM user WHERE user_id = :userId');
		if (empty($result = Database::executeSelect([":userId" => $userId]))) {
		    return null;
        }

		return $result[0][self::NAME];
	}

    /**
     * @param int $userId
     * @return string|null
     * @throws \Exception
     */
	public function getTagColor(int $userId): ?string
    {
		Database::prepareQuery('SELECT color FROM user WHERE user_id = :userId');
		if (empty($result = Database::executeSelect([":userId" => $userId]))) {
		    return null;
        }

		return $result[0][self::TAG_COLOR];	
	}

    /**
     * @param int $userId
     * @return bool
     * @throws \Exception
     */
	public function isUserActive(int $userId): bool
    {
		Database::prepareQuery('SELECT active from user WHERE user_id = :userId');
		if (empty($result = Database::executeSelect([":userId" => $userId]))) {
		    return false;
        }

		return $result[0]["active"] == 1 ? true : false;
	}

    /**
     * @param int $userId
     * @return bool
     * @throws \Exception
     */
	public function isUserAdmin(int $userId): bool
    {
		Database::prepareQuery(
		    'SELECT STRCMP(Roles.Name, :roleName) AS result FROM user ' .
            'LEFT JOIN Roles ON user.Role = Roles.ID WHERE user_id = :userId'
        );

		if (empty($result = Database::executeSelect([":userId" => $userId, ":roleName" => Role::ADMIN_ROLE]))) {
            throw new \Exception("User $userId doesn't exist.");
        }
		
		return ($result[0]["result"] == 0 ? true : false);
	}

    /**
     * @return array
     * @throws \Exception
     */
	public function getActiveUsers(): array
    {
		Database::prepareQuery('SELECT * FROM user WHERE active = \'1\' ORDER BY active, username');
		return Database::executeSelect();
	}

    /**
     * @return array
     * @throws \Exception
     */
    public function getAllUsers() : array
    {
		Database::prepareQuery('SELECT * FROM user ORDER BY active DESC, username');
		return Database::executeSelect();
	}

    /**
     * @param int $userId
     * @return int
     * @throws \Exception
     */
	public function countOtherAdminUsers(int $userId): int
    {
		Database::prepareQuery('SELECT COUNT(*) AS result FROM user WHERE role = :adminRoleId AND user_id <> :userId');
		if (empty($result = Database::executeSelect([":userId" => $userId, ":adminRoleId" => Role::ADMIN_ID]))) {
		    return 0;
        }

		return $result[0]["result"];
	}

    /**
     * @param array $userData
     * @return bool
     * @throws \Exception
     */
	public function insertUser(array $userData): bool
    {
		if (empty($userData)) {
		    return false;
        }

		$fields = "( ";
		$valuesNames = "( ";
		$values = array();
		
		foreach($userData as $key => $value){
			$fields .= $key;
			$valuesNames .= ":".$key;
			$values[":".$key] = $value;
			if(end($userData) !== $value){
				$fields .= ", ";
				$valuesNames .= ", ";
			}
		}
		$fields .= " )";
		$valuesNames .= " )";

		Database::prepareQuery("INSERT INTO user $fields VALUES $valuesNames");
		return Database::executeInsert($values);
	}

    /**
     * @param array $userData
     * @return bool
     * @throws \Exception
     */
	public function updateUser(array $userData): bool
    {
		if (empty($userData)) {
            return false;
        }
			
		$userId = $userData["itemID"];
		unset($userData["itemID"]);
		$fields = '';
		$values = [];
		
		foreach ($userData as $key => $value) {
			$fields .= $key . ' = :' . $key;
			$values[':'.$key] = $value;
			if (end($userData) !== $value) {
                $fields .= ', ';
            }

		}
		$values[':userId'] = $userId;
		Database::prepareQuery("UPDATE user SET $fields WHERE user_id = :userId");
		return Database::executeUpdate($values);
	}
}
