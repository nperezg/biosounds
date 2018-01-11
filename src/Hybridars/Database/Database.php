<?php

namespace Hybridars\Database;

class Database
{
    /**
     * @var \PDO
     */
	public static $connection;

    /**
     * @var \PDOStatement
     */
	private static $stmt;
	
	function __construct(){
	}
	
	/**
	 * Prepares a query
	 *
     * @throws \Exception
	 */
	public static function prepareQuery($query)
	{
		self::$stmt = self::$connection->prepare($query);
		if(!self::$stmt) {
			error_log("The query is wrong and couldn't be prepared.");
			error_log("Executing query: ".$query);
			throw new \Exception("The query is wrong and couldn't be prepared.");	
		}	
	}
	
	/**
	 * Executes a prepared query
	 *
	 * @param array $values values of the query
	 */
	public static function executeSelect(array $values = NULL)
	{
		return self::executeQuery($values, 1);
	}
	
	/**
	 * Insert a row in the given table
	 *
	 * @param array $data
	 * @return integer|null
	 */
	public static function executeInsert(array $values = NULL)
	{
		return self::executeQuery($values, 2);
	}

	/**
	 * Update a Database row
	 *
	 * @param string $table name
	 * @param array $data
	 * @param array $w where conditions
	 * @return integer|null
	 */
	public static function executeUpdate(array $values = NULL)
	{
		return self::executeQuery($values, 3);
	}
	
	/**
	 * Delete rows from Database
	 *
	 * @param array $values
	 * @return integer|null
	 */
	public static function executeDelete(array $values = NULL)
	{
		return self::executeQuery($values, 4);
	}
	
	/**
	 * Executes a prepared query
	 *
	 * @param array $values values of the query
	 * @param array $queryType type of query (1: Select, 2: Insert, 3: Update)
	 */
	private static function executeQuery(array $values = NULL, $queryType)
	{
		try {
			if(!self::$stmt)
				throw new \Exception('No statement to execute. Error: ' . self::$connection->errorCode(), E_USER_ERROR);
		
			if($values != NULL){
				if(!self::$stmt->execute($values)) {
					error_log('Error when executing the querytype: ' . implode(',',self::$stmt->errorInfo()));
				}
			} else			 
				self::$stmt->execute();	
			
			switch ($queryType) {
				case 1 : 
					$result = self::$stmt->fetchAll(\PDO::FETCH_ASSOC);
					return $result;
				case 2:
					return self::$connection->lastInsertId();
				case 3:
					return self::$stmt->rowCount();	
				case 4:
					return self::$stmt->rowCount();	
			}	
		} catch(\Exception $e){
			error_log($e->getMessage());
			error_log("Executing query: ".self::$stmt->queryString);
			if(!empty($values))
				error_log("Valores: ".implode(", ", $values));
			throw new \Exception($e);
		}	 
	}
}
