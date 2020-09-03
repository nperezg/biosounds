<?php

namespace BioSounds\Database;

use Exception;
use PDO;
use PDOStatement;

class Database
{
    const CONNECTION_STRING = '%s:host=%s;dbname=%s;port=3306';
    /**
     * @var PDO
     */
    private $connection;

    /**
     * @var PDOStatement
     */
    private $stmt;

    /**
     * @var string
     */
    private $dsn;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * Database constructor.
     * @param string $driver
     * @param string $host
     * @param string $database
     * @param string $user
     * @param string $password
     */
    public function __construct(string $driver, string $host, string $database, string $user, string $password)
    {
        $this->dsn = sprintf(self::CONNECTION_STRING, $driver, $host, $database);
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @param string $query
     * @throws Exception
     */
    public function prepareQuery(string $query)
    {
        $this->initConnection();
        $this->stmt = $this->connection->prepare($query);
    }

    /**
     * @param array|null $values
     * @return array|int|string
     */
    public function executeSelect(array $values = null)
    {
        return $this->executeQuery(1, $values);
    }

    /**
     * @param array|null $values
     * @return array|int
     * @throws Exception
     */
    public function executeInsert(array $values = null)
    {
        return $this->executeQuery(2, $values);
    }

    /**
     * @param array|null $values
     * @return array|int
     * @throws Exception
     */
    public function executeUpdate(array $values = null)
    {
        return $this->executeQuery(3, $values);
    }

    /**
     * @param array|null $values
     * @return array|int
     * @throws Exception
     */
    public function executeDelete(array $values = null)
    {
        return $this->executeQuery(4, $values);
    }

    /**
     * @param int $queryType
     * @param array|null $values
     * @return array|int|string
     */
    private function executeQuery(int $queryType, array $values = null)
    {
        $this->stmt->execute($values);

        $result = null;

        switch ($queryType) {
            case 1:
                $result = $this->stmt->fetchAll();
                break;
            case 2:
                $result = $this->connection->lastInsertId();
                break;
            case 3:
            case 4:
                $result = $this->stmt->rowCount();
        }

        // Close connection
        $this->stmt = null;
        $this->connection = null;
        return $result;
    }

    private function initConnection()
    {
        if ($this->connection === null) {
            $this->connection = new PDO(
                $this->dsn,
                $this->user,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }
    }
}
