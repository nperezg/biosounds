<?php

namespace Hybridars\BioSounds\Presenter;

/**
 * Class FrequencyScalePresenter
 * @package Hybridars\BioSounds\Presenter
 */
class FrequencyScalePresenter
{
    private $freqMaxHeight;

    private $freqMax;

    private $freqMid2Height;

    private $freqMid2;

    private $freqMid1Height;

    private $freqMid1;

    private $freqMinHeight;

    private $freqMin;

    /**
     * @return mixed
     */
    public function getFreqMaxHeight()
    {
        return $this->freqMaxHeight;
    }

    /**
     * @param mixed $freqMaxHeight
     * @return FrequencyScalePresenter
     */
    public function setFreqMaxHeight($freqMaxHeight)
    {
        $this->freqMaxHeight = $freqMaxHeight;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFreqMax()
    {
        return $this->freqMax;
    }

    /**
     * @param mixed $freqMax
     * @return FrequencyScalePresenter
     */
    public function setFreqMax($freqMax)
    {
        $this->freqMax = $freqMax;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFreqMid2Height()
    {
        return $this->freqMid2Height;
    }

    /**
     * @param mixed $freqMid2Height
     * @return FrequencyScalePresenter
     */
    public function setFreqMid2Height($freqMid2Height)
    {
        $this->freqMid2Height = $freqMid2Height;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFreqMid2()
    {
        return $this->freqMid2;
    }

    /**
     * @param mixed $freqMid2
     * @return FrequencyScalePresenter
     */
    public function setFreqMid2($freqMid2)
    {
        $this->freqMid2 = $freqMid2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFreqMid1Height()
    {
        return $this->freqMid1Height;
    }

    /**
     * @param mixed $freqMid1Height
     * @return FrequencyScalePresenter
     */
    public function setFreqMid1Height($freqMid1Height)
    {
        $this->freqMid1Height = $freqMid1Height;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFreqMid1()
    {
        return $this->freqMid1;
    }

    /**
     * @param mixed $freqMid1
     * @return FrequencyScalePresenter
     */
    public function setFreqMid1($freqMid1)
    {
        $this->freqMid1 = $freqMid1;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFreqMinHeight()
    {
        return $this->freqMinHeight;
    }

    /**
     * @param mixed $freqMinHeight
     * @return FrequencyScalePresenter
     */
    public function setFreqMinHeight($freqMinHeight)
    {
        $this->freqMinHeight = $freqMinHeight;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFreqMin()
    {
        return $this->freqMin;
    }

    /**
     * @param mixed $freqMin
     * @return FrequencyScalePresenter
     */
    public function setFreqMin($freqMin)
    {
        $this->freqMin = $freqMin;
        return $this;
    }
}
