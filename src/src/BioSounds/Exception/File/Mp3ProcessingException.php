<?php

namespace BioSounds\Exception\File;

/**
 * Class Mp3ProcessingException
 * @package BioSounds\Exception\File
 */
class Mp3ProcessingException extends \Exception
{
    const MESSAGE = 'Error processing mp3 sound for file %s: %s';

    /**
     * Mp3ProcessingException constructor.
     * @param string $fileName
     * @param string $errorMessage
     */
    public function __construct(string $fileName, string $errorMessage)
    {
        parent::__construct(sprintf($this::MESSAGE, $fileName, $errorMessage));
    }
}