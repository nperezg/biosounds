<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Entity\Animal;

class AnimalController
{
    public function getAnimalList(){
		if(isset($_REQUEST['term'])){
			$words = preg_split("/[\s,]+/", $_REQUEST['term']);

			$animal = new Animal;
			$result = $animal->getAnimalList($words);

			if(!empty($result)){
				foreach($result as $row){
					$data[] = array('label' => $row['Binomial'] .' ( '. $row['CommonName'] .' ) ', 'value' => $row['AnimalID'] );					
				}
				return $data;
			}
		}	
		return $data[] = array('label' => "No results", 'value' => 0);
	}
}