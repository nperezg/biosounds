<?php

namespace Hybridars\BioSounds\Exception\File;

/**
 * Class FileInvalidException
 * @package Hybridars\BioSounds\Exception\File
 */
class FileInvalidException extends \Exception
{
    const MESSAGE = '%s is not a valid file.';

    /**
     * FileInvalidException constructor.
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        parent::__construct(sprintf($this::MESSAGE, $fileName));
    }
}