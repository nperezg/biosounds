<?php

namespace Hybridars\BioSounds\Entity;


class SoundImage
{
    const COLOR_PALETTE = 1;

    /**
     * @var int
     */
    private $soundImageId;

    /**
     * @var int
     */
    private $soundId;

    /**
     * @var string
     */
    private $imageFile;

    /**
     * @var string
     */
    private $imageType;

    /**
     * @var int
     */
    private $colorPalette = 1;

    /**
     * @var int
     */
    private $specMaxFreq;

    /**
     * @var int
     */
    private $imageFft;

    /**
     * @var string
     */
    private $imageCreator;

    /**
     * @return int
     */
    public function getSoundImageId(): int
    {
        return $this->soundImageId;
    }

    /**
     * @param int $soundImageId
     * @return SoundImage
     */
    public function setSoundImageId(int $soundImageId): SoundImage
    {
        $this->soundImageId = $soundImageId;
        return $this;
    }

    /**
     * @return int
     */
    public function getSoundId(): int
    {
        return $this->soundId;
    }

    /**
     * @param int $soundId
     * @return SoundImage
     */
    public function setSoundId(int $soundId): SoundImage
    {
        $this->soundId = $soundId;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getImageFile(): ?string
    {
        return $this->imageFile;
    }

    /**
     * @param null|string $imageFile
     * @return SoundImage
     */
    public function setImageFile(?string $imageFile): SoundImage
    {
        $this->imageFile = $imageFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getImageType(): string
    {
        return $this->imageType;
    }

    /**
     * @param string $imageType
     * @return SoundImage
     */
    public function setImageType(string $imageType): SoundImage
    {
        $this->imageType = $imageType;
        return $this;
    }

    /**
     * @return int
     */
    public function getColorPalette(): int
    {
        return $this->colorPalette;
    }

    /**
     * @param int $colorPalette
     * @return SoundImage
     */
    public function setColorPalette(int $colorPalette): SoundImage
    {
        $this->colorPalette = $colorPalette;
        return $this;
    }

    /**
     * @return int
     */
    public function getSpecMaxFreq(): int
    {
        return $this->specMaxFreq;
    }

    /**
     * @param int $specMaxFreq
     * @return SoundImage
     */
    public function setSpecMaxFreq(int $specMaxFreq): SoundImage
    {
        $this->specMaxFreq = $specMaxFreq;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getImageFft():? int
    {
        return $this->imageFft;
    }

    /**
     * @param int $imageFft
     * @return SoundImage
     */
    public function setImageFft(int $imageFft): SoundImage
    {
        $this->imageFft = $imageFft;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getImageCreator():? string
    {
        return $this->imageCreator;
    }

    /**
     * @param string $imageCreator
     * @return SoundImage
     */
    public function setImageCreator(string $imageCreator): SoundImage
    {
        $this->imageCreator = $imageCreator;
        return $this;
    }

    /**
     * @return array
     */
    public function getDatabaseValues(): array
    {
        return [
            ':recordingId' => $this->getSoundId(),
            ':filename' => $this->getImageFile(),
            ':type' => $this->getImageType(),
            ':palette' => $this->getColorPalette(),
            ':specMaxFreq' => $this->getSpecMaxFreq(),
        ];
    }
}