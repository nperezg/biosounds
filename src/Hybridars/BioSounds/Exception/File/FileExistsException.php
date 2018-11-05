<?php

namespace Hybridars\BioSounds\Exception\File;

class FileExistsException extends \Exception
{
    const MESSAGE = 'File %s already exists in the system.';

    /**
     * FileExistsException constructor.
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        parent::__construct(sprintf($this::MESSAGE, $fileName));
    }
}
