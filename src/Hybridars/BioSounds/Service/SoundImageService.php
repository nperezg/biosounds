<?php

namespace Hybridars\BioSounds\Service;

use Hybridars\BioSounds\Entity\SoundImage;
use Hybridars\BioSounds\Provider\SoundImageProvider;

class SoundImageService
{
    /**
     * @var SoundImageProvider
     */
    private $soundImageProvider;

    public function __construct()
    {
        $this->soundImageProvider = new SoundImageProvider();
    }

    /**
     * @param int $soundId
     * @param string $filePath
     * @param string $imageType
     * @param int $maxFrequency
     * @throws \Exception
     */
    public function insert(int $soundId, string $filePath, string $imageType, int $maxFrequency)
    {
        $this->soundImageProvider->insert(
            (new SoundImage())
                ->setSoundId($soundId)
                ->setImageFile($filePath)
                ->setImageType($imageType)
                ->setColorPalette(SoundImage::COLOR_PALETTE)
                ->setSpecMaxFreq($maxFrequency)
        );
    }

    /**
     * @param int $recordingId
     * @return SoundImage|null
     * @throws \Exception
     */
    public function getSmallImage(int $recordingId): ?SoundImage
    {
        return $this->soundImageProvider->get($recordingId, 'spectrogram-small');
    }

    /**
     * @param int $recordingId
     * @return SoundImage|null
     * @throws \Exception
     */
    public function getPlayerImage(int $recordingId): ?SoundImage
    {
        return $this->soundImageProvider->get($recordingId, 'spectrogram-player');
    }

    /**
     * @param int $recordingId
     * @throws \Exception
     */
    public function deleteByRecording(int $recordingId)
    {
        $this->soundImageProvider->deleteByRecording($recordingId);
    }
}
