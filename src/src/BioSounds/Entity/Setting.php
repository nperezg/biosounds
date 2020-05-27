<?php 

namespace BioSounds\Entity;

use BioSounds\Provider\BaseProvider;

class Setting extends BaseProvider
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
		$this->database->prepareQuery('SELECT * FROM setting');
		$result = $this->database->executeSelect();
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
        $this->database->prepareQuery('SELECT value FROM setting WHERE name = :name');
		$result = $this->database->executeSelect([':name' => $name]);
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
        $this->database->prepareQuery(
		    'UPDATE ' . self::TABLE_NAME. ' SET ' . self::VALUE . '= :value WHERE ' . self::NAME. '= :name'
        );
		return $this->database->executeUpdate([':name' => $name, ':value' => $value]);
	}
}
