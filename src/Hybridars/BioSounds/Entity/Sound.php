<?php

namespace Hybridars\BioSounds\Entity;


class Sound
{
    const TABLE_NAME = 'sound';
    const SPECIES_ID = 'species_id';
    const RATING = 'rating';

    /**
     * @var int
     */
    private $sound_id;

    /**
     * @var int
     */
    private $species;

    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $subtype;

    /**
     * @var int
     */
    private $distance;

    /**
     * @var bool
     */
    private $notEstimableDistance;

    /**
     * @var int
     */
    private $individualNum = 1;

    /**
     * @var bool
     */
    private $uncertain = false;

    /**
     * @var string
     */
    private $rating;

    /**
     * @var string
     */
    private $note;

    /**
     * TODO: this property should be in species property, when it's an object, using ORM
     * @var string
     */
    private $speciesName;

    /**
     * TODO: this property should be in type property, when it's an object, using ORM
     * @var string
     */
    private $typeName;

    /**
     * @return int
     */
    public function getSoundId(): int
    {
        return $this->sound_id;
    }

    /**
     * @param int $sound_id
     * @return Sound
     */
    public function setSoundId(int $sound_id): Sound
    {
        $this->sound_id = $sound_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getSpecies(): int
    {
        return $this->species;
    }

    /**
     * @param int $species
     * @return Sound
     */
    public function setSpecies(int $species): Sound
    {
        $this->species = $species;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Sound
     */
    public function setType(int $type): Sound
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSubtype(): ?string
    {
        return $this->subtype;
    }

    /**
     * @param null|string $subtype
     * @return Sound
     */
    public function setSubtype(?string $subtype): Sound
    {
        $this->subtype = $subtype;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDistance(): ?int
    {
        return $this->distance;
    }

    /**
     * @param int|null $distance
     * @return Sound
     */
    public function setDistance(?int $distance): Sound
    {
        $this->distance = $distance;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isNotEstimableDistance(): ?bool
    {
        return $this->notEstimableDistance;
    }

    /**
     * @param bool|null $notEstimableDistance
     * @return Sound
     */
    public function setNotEstimableDistance(?bool $notEstimableDistance): Sound
    {
        $this->notEstimableDistance = $notEstimableDistance;
        return $this;
    }

    /**
     * @return int
     */
    public function getIndividualNum(): int
    {
        return $this->individualNum;
    }

    /**
     * @param int $individualNum
     * @return Sound
     */
    public function setIndividualNum(int $individualNum): Sound
    {
        $this->individualNum = $individualNum;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUncertain(): bool
    {
        return $this->uncertain;
    }

    /**
     * @param bool $uncertain
     * @return Sound
     */
    public function setUncertain(bool $uncertain): Sound
    {
        $this->uncertain = $uncertain;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getRating(): ?string
    {
        return $this->rating;
    }

    /**
     * @param null|string $rating
     * @return Sound
     */
    public function setRating(?string $rating): Sound
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param null|string $note
     * @return Sound
     */
    public function setNote(?string $note): Sound
    {
        $this->note = $note;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSpeciesName(): ?string
    {
        return $this->speciesName;
    }

    /**
     * @param null|string $speciesName
     * @return Sound
     */
    public function setSpeciesName(?string $speciesName): Sound
    {
        $this->speciesName = $speciesName;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getTypeName(): ?string
    {
        return $this->typeName;
    }

    /**
     * @param null|string $typeName
     * @return Sound
     */
    public function setTypeName(?string $typeName): Sound
    {
        $this->typeName = $typeName;
        return $this;
    }

    /**
     * @return array
     */
    public function getDatabaseValues(): array
    {
        return [
            ':species' => $this->getSpecies(),
            ':type' => $this->getType(),
            ':subtype' => $this->getSubtype(),
            ':distance' => $this->getDistance(),
            ':notEstimableDistance' => (int)$this->isNotEstimableDistance(),
            ':individualNum' => $this->getIndividualNum(),
            ':uncertain' => (int)$this->isUncertain(),
            ':rating' => $this->getRating(),
            ':note' => $this->getNote(),
        ];
    }
}
