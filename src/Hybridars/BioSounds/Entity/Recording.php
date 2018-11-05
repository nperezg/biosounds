<?php

namespace Hybridars\BioSounds\Entity;


class Recording
{
	const TABLE_NAME = 'recording';
	const ID = 'recording_id';
	const COL_ID = 'col_id';
    const SITE_ID = 'site_id';
    const SENSOR_ID = 'sensor_id';
    const SOUND_ID = 'sound_id';
	const DIRECTORY = 'directory';
	const FILE_SIZE = "file_size";
	const NAME = "name";
	const FILENAME = "filename";
	const FILE_DATE = "file_date";
	const FILE_TIME = "file_time";
	const DURATION = "duration";
	const CHANNEL_NUM = "channel_num";
	const SAMPLING_RATE = "sampling_rate";
	const BITRATE = 'bitrate';
	const MD5_HASH = 'md5_hash';

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $collection;

    /**
     * @var int
     */
    private $directory;

    /**
     * @var int
     */
    private $site;

    /**
     * @var int
     */
    private $sensor;

    /**
     * @var int
     */
    private $sound;

    /**
     * @var int
     */
    private $fileSize;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $fileDate;

    /**
     * @var string
     */
    private $fileTime;

    /**
     * @var int
     */
    private $duration;

    /**
     * @var int
     */
    private $channelNum;

    /**
     * @var int
     */
    private $samplingRate;

    /**
     * @var int
     */
    private $bitrate;

    /**
     * TODO: this should be property 'sound', when using an ORM
     * @var Sound
     */
    private $soundData;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Recording
     */
    public function setId(int $id): Recording
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
     * @return Recording
     */
    public function setName(string $name): Recording
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getCollection(): int
    {
        return $this->collection;
    }

    /**
     * @param int $collection
     * @return Recording
     */
    public function setCollection(int $collection): Recording
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @return int
     */
    public function getDirectory(): int
    {
        return $this->directory;
    }

    /**
     * @param int $directory
     * @return Recording
     */
    public function setDirectory(int $directory): Recording
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSite(): ?int
    {
        return $this->site;
    }

    /**
     * @param int|null $site
     * @return Recording
     */
    public function setSite(?int $site): Recording
    {
        $this->site = $site;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSensor(): ?int
    {
        return $this->sensor;
    }

    /**
     * @param int|null $sensor
     * @return Recording
     */
    public function setSensor(?int $sensor): Recording
    {
        $this->sensor = $sensor;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSound(): ?int
    {
        return $this->sound;
    }

    /**
     * @param int|null $sound
     * @return Recording
     */
    public function setSound(?int $sound): Recording
    {
        $this->sound = $sound;
        return $this;
    }

    /**
     * @return int
     */
    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    /**
     * @param int $fileSize
     * @return Recording
     */
    public function setFileSize(int $fileSize): Recording
    {
        $this->fileSize = $fileSize;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return Recording
     */
    public function setFileName(string $fileName): Recording
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getFileDate(): ?string
    {
        return $this->fileDate;
    }

    /**
     * @param null|string $fileDate
     * @return Recording
     */
    public function setFileDate(?string $fileDate): Recording
    {
        $this->fileDate = $fileDate;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getFileTime(): ?string
    {
        return $this->fileTime;
    }

    /**
     * @param null|string $fileTime
     * @return Recording
     */
    public function setFileTime(?string $fileTime): Recording
    {
        $this->fileTime = $fileTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     * @return Recording
     */
    public function setDuration(int $duration): Recording
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * @return int
     */
    public function getChannelNum(): int
    {
        return $this->channelNum;
    }

    /**
     * @param int $channelNum
     * @return Recording
     */
    public function setChannelNum(int $channelNum): Recording
    {
        $this->channelNum = $channelNum;
        return $this;
    }

    /**
     * @return int
     */
    public function getSamplingRate(): int
    {
        return $this->samplingRate;
    }

    /**
     * @param int $samplingRate
     * @return Recording
     */
    public function setSamplingRate(int $samplingRate): Recording
    {
        $this->samplingRate = $samplingRate;
        return $this;
    }

    /**
     * @return int
     */
    public function getBitrate(): int
    {
        return $this->bitrate;
    }

    /**
     * @param int $bitrate
     * @return Recording
     */
    public function setBitrate(int $bitrate): Recording
    {
        $this->bitrate = $bitrate;
        return $this;
    }

    /**
     * @return Sound|null
     */
    public function getSoundData(): ?Sound
    {
        return $this->soundData;
    }

    /**
     * @param Sound|null $soundData
     * @return Recording
     */
    public function setSoundData(?Sound $soundData): Recording
    {
        $this->soundData = $soundData;
        return $this;
    }
}
