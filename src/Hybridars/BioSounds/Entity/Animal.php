<?php

namespace Hybridars\BioSounds\Entity;

use Hybridars\BioSounds\Database\Database;

class Animal
{
	const TABLE_NAME = "Animals";
	const ID = "AnimalID";
	const BINOMIAL = "Binomial";
	
    public function getAnimalList($names){
		$query = "SELECT AnimalID, Binomial, CommonName FROM Animals";
		$fields = array();
		if(isset($names)){
			if(count($names) == 1){
				$query .= " WHERE Binomial LIKE :binomial OR CommonName LIKE :name ";
				$fields = [":binomial" => "%$names[0]%", ":name" => "%$names[0]%"];
			}
			else{	
				$query .= " WHERE (Binomial LIKE :binomial1 AND Binomial LIKE :binomial2) OR (CommonName LIKE :name1 AND CommonName LIKE :name2) ";
				$fields = [":binomial1" => "%$names[0]%", ":binomial2" => "%$names[1]%", ":name1" => "%$names[0]%", ":name2" => "%$names[1]%"];
			}
		}
		$query .= "ORDER BY Binomial ASC LIMIT 0,15";
		Database::prepareQuery($query);
		$result = Database::executeSelect($fields);
		return $result;
	}
}
