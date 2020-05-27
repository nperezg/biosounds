<?php

namespace BioSounds\Entity;

use BioSounds\Provider\BaseProvider;

abstract class AbstractProvider extends BaseProvider
{
	const TABLE_NAME = ''; 
	const PRIMARY_KEY = '';
	const NAME = '';

    /**
     * @return array|int
     * @throws \Exception
     */
    public function getBasicList()
    {
		$this->database->prepareQuery(
		    'SELECT '.static::PRIMARY_KEY.', '.static::NAME.' FROM ' . static::TABLE_NAME . ' ORDER BY '.static::NAME
        );
		return $this->database->executeSelect();
	}
}
