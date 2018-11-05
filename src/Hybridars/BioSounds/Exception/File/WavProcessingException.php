<?php

namespace Hybridars\BioSounds\Exception\File;

/**
 * Class WavProcessingException
 * @package Hybridars\BioSounds\Exception\File
 */
class WavProcessingException extends \Exception
{
    const MESSAGE = 'Error processing wav sound for file %s: %s';

    /**
     * WavProcessingException constructor.
     * @param string $fileName
     * @param string $errorMessage
     */
    public function __construct(string $fileName, string $errorMessage)
    {
        parent::__construct(sprintf($this::MESSAGE, $fileName, $errorMessage));
    }
}