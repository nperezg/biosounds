<?php 

namespace Hybridars\BioSounds\Entity;

use Hybridars\BioSounds\Database\Database;

class Setting
{
	
	const TABLE_NAME = 'setting';
	const NAME = 'name';
	const VALUE = 'value';
	
	const PROJECT_NAME = 'projectName';
	const PROJECT_DESCRIPTION = 'projectDescription';
	const FFT = 'FFT';
	const FILES_LICENSE = 'filesLicense';
	const FILES_LICENSE_DETAIL = 'filesLicenseDetail';

    /**
     * @return array
     * @throws \Exception
     */
	public function getList()
    {
		Database::prepareQuery('SELECT * FROM setting');
		$result = Database::executeSelect();
		$values = [];
		foreach ($result as $data) {
			$values[$data['name']] = $data['value'];
		}
		return $values;
	}

    /**
     * @param string $name
     * @return |null
     * @throws \Exception
     */
	public function getByName(string $name)
    {
		Database::prepareQuery('SELECT value FROM setting WHERE name = :name');
		$result = Database::executeSelect([':name' => $name]);
		if (empty($result)) {
		    return null;
        }

		return $result[0]['value'];
	}

    /**
     * @param string $name
     * @param string $value
     * @return array|int
     * @throws \Exception
     */
	public function update(string $name, string $value)
    {
		Database::prepareQuery(
		    'UPDATE ' . self::TABLE_NAME. ' SET ' . self::VALUE . '= :value WHERE ' . self::NAME. '= :name'
        );
		return Database::executeUpdate([':name' => $name, ':value' => $value]);
	}
}
