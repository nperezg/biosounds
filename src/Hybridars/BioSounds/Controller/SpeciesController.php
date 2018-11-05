<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Entity\Species;

class SpeciesController
{
    /**
     * @return array
     * @throws \Exception
     */
    public function getList(): array
    {
        $data[] = ['label' => 'No results', 'value' => '0'];

		if (isset($_REQUEST['term'])) {
			$words = preg_split("/[\s,]+/", $_REQUEST['term']);

			$animal = new Species;
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
        return $data;
	}
}
