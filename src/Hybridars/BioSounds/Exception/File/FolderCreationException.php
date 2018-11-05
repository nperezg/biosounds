<?php

namespace Hybridars\BioSounds\Exception\File;

/**
 * Class FolderCreationException
 * @package Hybridars\BioSounds\Exception\File
 */
class FolderCreationException extends \Exception
{
    const MESSAGE = 'Folder %s cannot be created.';

    /**
     * FolderCreationException constructor.
     * @param string $folderPath
     */
    public function __construct(string $folderPath)
    {
        parent::__construct(sprintf($this::MESSAGE));
    }
}
