<?php

namespace BioSounds\Exception\File;

/**
 * Class FileNotFoundException
 * @package BioSounds\Exception\File
 */
class FileNotFoundException extends \Exception
{
    const MESSAGE = 'File or folder %s not found.';

    /**
     * FileNotFoundException constructor.
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        parent::__construct(sprintf($this::MESSAGE, $fileName), 404);
    }
}