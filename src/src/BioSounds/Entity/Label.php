<?php

namespace BioSounds\Entity;

use BioSounds\Provider\BaseProvider;

class Label extends BaseProvider
{
    const TABLE_NAME = "label";
    const PRIMARY_KEY = "label_id";
    const NAME = "name";
    const CUSTOMIZED = 'customized';
    const CREATION_DATE = "creation_date";

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $creationDate;

    /**
     * @var bool
     */
    private $customized;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return label
     */
    public function setId(int $id): Label
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return label
     */
    public function setName(string $name): Label
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreationDate(): string
    {
        return $this->creationDate;
    }

    /**
     * @param string $creationDate
     * @return label
     */
    public function setCreationDate(string $creationDate): Label
    {
        $this->creationDate = $creationDate;
        return $this;
    }

    /**
     * @return bool
     */
    public function getCustomized(): bool
    {
        return $this->customized;
    }

    /**
     * @param string $customized
     * @return label
     */
    public function setCustomized(string $customized): Label
    {
        $this->customized = $customized;
        return $this;
    }

    /**
     * @param array $labelData
     * @return bool
     * @throws \Exception
     */
    public function insert(array $labelData): bool
    {
        if (empty($labelData)) {
            return false;
        }

        $fields = "( ";
        $valuesNames = "( ";
        $values = array();

        foreach ($labelData as $key => $value) {
            $fields .= $key;
            $valuesNames .= ":" . $key;
            $values[":" . $key] = $value;
            if (end($siteData) !== $value) {
                $fields .= ", ";
                $valuesNames .= ", ";
            }
        }
        $fields .= " )";
        $valuesNames .= " )";

        $this->database->prepareQuery("INSERT INTO label $fields VALUES $valuesNames");
        return $this->database->executeInsert($values);
    }


    /**
     * @param array $labelData
     * @return bool
     * @throws \Exception
     */
    public function update(array $labelData): bool
    {
        if (empty($labelData)) {
            return false;
        }

        $lbeId = $labelData["lbeId"];
        unset($labelData["lbeId"]);
        $fields = '';
        $values = [];

        foreach ($labelData as $key => $value) {
            $fields .= $key . ' = :' . $key;
            $values[':' . $key] = $value;
            if (end($labelData) !== $value) {
                $fields .= ', ';
            }
        }

        $values[':labelId'] = $lbeId;
        $this->database->prepareQuery("UPDATE label SET $fields WHERE label_id = :labelId");
        return $this->database->executeUpdate($values);
    }

    public function getList($term)
    {
        $query = 'SELECT ' . self::PRIMARY_KEY . ',' . self::NAME .
            ' FROM ' . self::TABLE_NAME .
            ' WHERE ' . self::NAME . ' LIKE :name ' .
            ' ORDER BY ' . self::NAME . ' ASC LIMIT 0,15';

        $field = ['name' => "%$term%"];

        $this->database->prepareQuery($query);
        $result = $this->database->executeSelect($field);

        return $result;
    }
}
