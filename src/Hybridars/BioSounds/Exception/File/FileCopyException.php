<?php

namespace Hybridars\BioSounds\Exception\File;

/**
 * Class FileCopyException
 * @package Hybridars\BioSounds\Exception\File
 */
class FileCopyException extends \Exception
{
    const MESSAGE = 'File %s cannot be copied to %s.';

    /**
     * FileCopyException constructor.
     * @param string $originFilePath
     * @param string $destinationFilePath
     */
    public function __construct(string $originFilePath, string $destinationFilePath)
    {
        parent::__construct(sprintf($this::MESSAGE, $originFilePath, $destinationFilePath));
    }
}
