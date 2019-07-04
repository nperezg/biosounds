<?php

namespace BioSounds\Controller;

use BioSounds\Entity\Species;

class SpeciesController
{
    /**
     * @return string
     * @throws \Exception
     */
    public function getList(): string
    {
        $data = [];

        $terms = isset($_POST['term']) ? $_POST['term'] : null;

		if (!empty($terms)) {
			$words = preg_split("/[\s,]+/", $terms);

			$animal = new Species;
			error_log($words);
			$result = $animal->getList($words);

			if (!empty($result)) {
			    $data = [];
				foreach($result as $row){
					$data[] = [
					    'label' => $row[Species::BINOMIAL] .' ( '. $row[Species::NAME] .' ) ',
                        'value' => $row[Species::ID]
                    ];
				}
			}
		}
        return json_encode($data);
	}
}
