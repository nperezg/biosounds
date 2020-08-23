<?php

namespace BioSounds\Presenter;

use BioSounds\Entity\Recording;

class RecordingListPresenter
{
    /**
     * @var Recording
     */
    private $recording;

    /**
     * @var string
     */
    private $smallImage;

    /**
     * @var string
     */
    private $playerImage;

    /**
     * @var string
     */
    private $playerRecording;

    /**
     * @return Recording
     */
    public function getRecording(): Recording
    {
        return $this->recording;
    }

    /**
     * @param Recording $recording
     * @return RecordingListPresenter
     */
    public function setRecording(Recording $recording): RecordingListPresenter
    {
        $this->recording = $recording;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSmallImage(): ?string
    {
        return $this->smallImage;
    }

    /**
     * @param null|string $smallImage
     * @return RecordingListPresenter
     */
    public function setSmallImage(?string $smallImage): RecordingListPresenter
    {
        $this->smallImage = $smallImage;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPlayerImage(): ?string
    {
        return $this->playerImage;
    }

    /**
     * @param null|string $playerImage
     * @return RecordingListPresenter
     */
    public function setPlayerImage(?string $playerImage): RecordingListPresenter
    {
        $this->playerImage = $playerImage;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlayerRecording(): string
    {
        return $this->playerRecording;
    }

    /**
     * @param string $playerRecording
     * @return RecordingListPresenter
     */
    public function setPlayerRecording(string $playerRecording): RecordingListPresenter
    {
        $this->playerRecording = $playerRecording;
        return $this;
    }
}
