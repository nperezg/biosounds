<?php

namespace Hybridars\BioSounds\Database;

class Database
{
    /**
     * @var \PDO
     */
	public static $connection;

    /**
     * @var \PDOStatement
     */
	public static $stmt;
	
    /**
     * @param string $query
     * @throws \Exception
     */
	public static function prepareQuery(string $query)
	{
		self::$stmt = self::$connection->prepare($query);
	}

    /**
     * @param array|null $values
     * @return array|int
     * @throws \Exception
     */
	public static function executeSelect(array $values = null)
	{
		return self::executeQuery($values, 1);
	}

    /**
     * @param array|null $values
     * @return array|int
     * @throws \Exception
     */
	public static function executeInsert(array $values = null)
	{
		return self::executeQuery($values, 2);
	}

    /**
     * @param array|null $values
     * @return array|int
     * @throws \Exception
     */
	public static function executeUpdate(array $values = null)
	{
		return self::executeQuery($values, 3);
	}

    /**
     * @param array|null $values
     * @return array|int
     * @throws \Exception
     */
	public static function executeDelete(array $values = null)
	{
		return self::executeQuery($values, 4);
	}

    /**
     * @param array|null $values
     * @param int $queryType
     * @return array|int|string
     */
	private static function executeQuery(array $values = null, int $queryType)
	{
        self::$stmt->execute($values);

        switch ($queryType) {
            case 1 :
                return self::$stmt->fetchAll();
            case 2:
                return self::$connection->lastInsertId();
            case 3:
                return self::$stmt->rowCount();
            case 4:
                return self::$stmt->rowCount();
        }
	}
}
