<?php

namespace Hybridars\BioSounds\Entity;


class SoundType
{
    /**
     * @var int
     */
    private $soundTypeId;

    /**
     * @var string
     */
    private $name;

    /**
     * @return int
     */
    public function getSoundTypeId(): int
    {
        return $this->soundTypeId;
    }

    /**
     * @param int $soundTypeId
     * @return SoundType
     */
    public function setSoundTypeId(int $soundTypeId): SoundType
    {
        $this->soundTypeId = $soundTypeId;
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
     * @return SoundType
     */
    public function setName(string $name): SoundType
    {
        $this->name = $name;
        return $this;
    }
}