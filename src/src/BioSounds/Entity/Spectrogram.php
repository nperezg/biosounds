<?php

namespace BioSounds\Entity;


class Spectrogram
{
    /**
     * @var int
     */
    private $spectrogramId;

    /**
     * @var int
     */
    private $recordingId;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $maxFrequency;

    /**
     * @var int
     */
    private $fft;


    /**
     * @return int
     */
    public function getSpectrogramId(): int
    {
        return $this->spectrogramId;
    }

    /**
     * @param int $spectrogramId
     * @return Spectrogram
     */
    public function setSpectrogramId(int $spectrogramId): Spectrogram
    {
        $this->spectrogramId = $spectrogramId;
        return $this;
    }

    /**
     * @return int
     */
    public function getRecordingId(): int
    {
        return $this->recordingId;
    }

    /**
     * @param int $recordingId
     * @return Spectrogram
     */
    public function setRecordingId(int $recordingId): Spectrogram
    {
        $this->recordingId = $recordingId;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return Spectrogram
     */
    public function setFilename(string $filename): Spectrogram
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Spectrogram
     */
    public function setType(string $type): Spectrogram
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxFrequency(): int
    {
        return $this->maxFrequency;
    }

    /**
     * @param int $maxFrequency
     * @return Spectrogram
     */
    public function setMaxFrequency(int $maxFrequency): Spectrogram
    {
        $this->maxFrequency = $maxFrequency;
        return $this;
    }

    /**
     * @return int
     */
    public function getFft(): int
    {
        return $this->fft;
    }

    /**
     * @param int $fft
     * @return Spectrogram
     */
    public function setFft(int $fft): Spectrogram
    {
        $this->fft = $fft;
        return $this;
    }

    /**
     * @return array
     */
    public function getDatabaseValues(): array
    {
        return [
            ':recordingId' => $this->getRecordingId(),
            ':filename' => $this->getFilename(),
            ':type' => $this->getType(),
            ':maxFrequency' => $this->getMaxFrequency(),
        ];
    }
}
