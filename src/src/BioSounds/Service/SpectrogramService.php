<?php

namespace BioSounds\Service;

use BioSounds\Entity\Spectrogram;
use BioSounds\Provider\SpectrogramProvider;

class SpectrogramService
{
    /**
     * @var SpectrogramProvider
     */
    private $spectrogramProvider;

    public function __construct()
    {
        $this->spectrogramProvider = new SpectrogramProvider();
    }

    /**
     * @param int $recordingId
     * @param string $filePath
     * @param string $type
     * @param int $maxFrequency
     * @throws \Exception
     */
    public function insert(int $recordingId, string $filePath, string $type, int $maxFrequency)
    {
        $this->spectrogramProvider->insert(
            (new Spectrogram())
                ->setRecordingId($recordingId)
                ->setFilename($filePath)
                ->setType($type)
                ->setMaxFrequency($maxFrequency)
        );
    }

    /**
     * @param int $recordingId
     * @return Spectrogram|null
     * @throws \Exception
     */
    public function getSmallImage(int $recordingId): ?Spectrogram
    {
        return $this->spectrogramProvider->get($recordingId, 'spectrogram-small');
    }

    /**
     * @param int $recordingId
     * @return Spectrogram|null
     * @throws \Exception
     */
    public function getPlayerImage(int $recordingId): ?Spectrogram
    {
        return $this->spectrogramProvider->get($recordingId, 'spectrogram-player');
    }

    /**
     * @param int $recordingId
     * @throws \Exception
     */
    public function deleteByRecording(int $recordingId)
    {
        $this->spectrogramProvider->deleteByRecording($recordingId);
    }
}
