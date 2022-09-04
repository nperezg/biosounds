<?php

namespace BioSounds\Entity;


use BioSounds\Provider\BaseProvider;

class IndexLog extends BaseProvider
{
    const TABLE_NAME = 'index_log';

    /**
     * @var int
     */
    private $log_id;

    /**
     * @var int
     */
    private $recording_id;

    /**
     * @var int
     */
    private $user_id;

    /**
     * @var int
     */
    private $index_id;

    /**
     * @var string
     */
    private $coordinates;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $param;

    /**
     * @var string
     */
    private $creation_date;

    /**
     * @return int
     */
    public function getLogId(): int
    {
        return $this->log_id;
    }

    /**
     * @param int $log_id
     * @return IndexLog
     */
    public function setLogId(int $log_id): IndexLog
    {
        $this->log_id = $log_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getRecordingId(): int
    {
        return $this->recording_id;
    }

    /**
     * @param int $recording_id
     * @return IndexLog
     */
    public function setRecordingId(int $recording_id): IndexLog
    {
        $this->recording_id = $recording_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     * @return IndexLog
     */
    public function setUserId(int $user_id): IndexLog
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getIndexId(): int
    {
        return $this->index_id;
    }

    /**
     * @param int $index_id
     * @return IndexLog
     */
    public function setIndexId(int $index_id): IndexLog
    {
        $this->index_id = $index_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCoordinates(): string
    {
        return $this->coordinates;
    }

    /**
     * @param string $coordinates
     * @return IndexLog
     */
    public function setCoordinates(string $coordinates): IndexLog
    {
        $this->coordinates = $coordinates;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return IndexLog
     */
    public function setValue(string $value): IndexLog
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getParam(): string
    {
        return $this->param;
    }

    /**
     * @param string $param
     * @return IndexLog
     */
    public function setParam(string $param): IndexLog
    {
        $this->param = $param;
        return $this;
    }


    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->creation_date;
    }

    /**
     * @param string $creation_date
     * @return IndexLog
     */
    public function setDate(string $creation_date): IndexLog
    {
        $this->creation_date = $creation_date;
        return $this;
    }

    /**
     * @return string
     */
    public function getRecordingName(): string
    {
        return $this->recording_name;
    }

    /**
     * @param string $recording_name
     * @return IndexLog
     */
    public function setRecordingName(string $recording_name): IndexLog
    {
        $this->recording_name = $recording_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->user_name;
    }

    /**
     * @param string $user_name
     * @return IndexLog
     */
    public function setUserName(string $user_name): IndexLog
    {
        $this->user_name = $user_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getIndexName(): string
    {
        return $this->index_name;
    }

    /**
     * @param string $index_name
     * @return IndexLog
     */
    public function setIndexName(string $index_name): IndexLog
    {
        $this->index_name = $index_name;
        return $this;
    }

    /**
     * @param array $indexDate
     * @return bool
     * @throws \Exception
     */
    public function insert(array $indexDate): bool
    {
        if (empty($indexDate)) {
            return false;
        }

        $fields = "( ";
        $valuesNames = "( ";
        $values = array();

        foreach ($indexDate as $key => $value) {
            $fields .= $key;
            $valuesNames .= ":" . $key;
            $values[":" . $key] = $value;
            if (end($indexDate) !== $value) {
                $fields .= ", ";
                $valuesNames .= ", ";
            }
        }
        $fields .= " )";
        $valuesNames .= " )";
        $this->database->prepareQuery("INSERT INTO index_log $fields VALUES $valuesNames");
        return $this->database->executeInsert($values);
    }
}
