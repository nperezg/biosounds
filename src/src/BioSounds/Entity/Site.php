<?php

namespace BioSounds\Entity;
use BioSounds\Provider\BaseProvider;

class Site extends AbstractProvider
{
	const TABLE_NAME = 'site';
	const PRIMARY_KEY = 'site_id';
	const NAME = 'name';
	const COUNTRY = 'country';

    public function getList($term)
    {
        $query = 'SELECT ' . self::PRIMARY_KEY .','. self::NAME .','. self::COUNTRY.
            ' FROM ' . self::TABLE_NAME .
            ' WHERE '. self::NAME . ' LIKE :name ' .
            ' ORDER BY ' . self::NAME . ' ASC LIMIT 0,15';

        $field = ['name' => "%$term%"];

        $this->database->prepareQuery($query);
        $result = $this->database->executeSelect($field);

        return $result;
    }
}
